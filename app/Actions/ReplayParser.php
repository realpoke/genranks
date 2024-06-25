<?php

namespace App\Actions;

use App\Contracts\ParsesReplayContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Exception\JsonException;

class ReplayParser implements ParsesReplayContract
{
    public function __invoke(string $file): Collection
    {
        if (Storage::disk('replays')->missing($file)) {
            Log::error('Did not find replay file: '.$file);

            return collect();
        }

        $replay = Storage::disk('replays')->path($file);
        $binary = $this->getReplayParserBinary();

        if ($binary === false) {
            Log::error('Did not find replay parser!');

            return collect();
        }

        $processResult = Process::env([
            'PATH' => '/usr/local/bin:/usr/bin:/bin',
        ])->run([$binary, $replay]);

        if (! $processResult->successful()) {
            Log::error('Failed to parse with error: '.$processResult->errorOutput());

            return collect();
        }

        try {
            $decodedData = collect(json_decode($processResult->output(), true, 512, JSON_BIGINT_AS_STRING));
        } catch (JsonException $e) {
            Log::error('Failed to decode json for '.$replay.': '.$e->getMessage());

            return collect();
        }

        $gameHash = $this->generateGameHash($decodedData);

        if (is_null($gameHash)) {
            Log::error('Failed to generate game hash');

            return collect();
        }

        $decodedData = $this->findSurrenderOrder($decodedData);

        return $this->cleanData($decodedData, $gameHash);
    }

    private function findSurrenderOrder(Collection $data): Collection
    {
        Log::debug('Finding surrender order');

        // Extract the 'Win' attribute from each player and get unique values
        $winValues = collect($data['Summary'])->pluck('Win')->unique();
        Log::debug('Initial Win Statuses:');
        foreach (collect($data['Summary']) as $player) {
            $winStatus = $player['Win'] ? 'true' : 'false';
            Log::debug("Player {$player['Name']} Win: {$winStatus}");
        }

        // Check if there is only one unique value in the 'Win' attributes
        if ($winValues->count() !== 1) {
            Log::debug('Not all players have the same win status.');

            return $data;
        }

        $surrenderedPlayer = null;
        Log::debug('Summary: '.collect($data['Summary']));

        // Process the body to find surrender commands
        foreach ($data['Body'] as $command) {
            if ($command['OrderName'] == 'Surrender' && $command['Arguments'][0] == true) {
                $surrenderedPlayer = $command['PlayerName'];
                break;
            }
        }

        // Only update the summary if a surrender command was found
        if ($surrenderedPlayer !== null) {
            $data['Summary'] = collect($data['Summary'])->map(function ($player) use ($surrenderedPlayer) {
                if ($player['Name'] == $surrenderedPlayer) {
                    $player['Win'] = false;
                } else {
                    $player['Win'] = true;
                }

                return $player;
            })->toArray();
        }

        Log::debug('Summary after surrender: '.collect($data['Summary']));

        return $data;
    }

    private function cleanData(Collection $data, string $hash): Collection
    {
        $summary = collect($data['Summary']);

        $header = collect($data['Header'])->only([
            'VersionMinor',
            'VersionMajor',
            'ReplayOwnerSlot',
            'GameSpeed',
        ]);
        // Calculate the new field 'ArrayReplayOwnerSlot'
        $arrayReplayOwnerSlot = (int) ($header->get('ReplayOwnerSlot') - 3000) / 100;
        $header = $header->put('ArrayReplayOwnerSlot', $arrayReplayOwnerSlot);

        // TODO: Make map hash, with map name and size so we can have a map list later
        $meta = collect($data['Header']['Metadata'])->only([
            'MapFile',
            'MapCRC',
            'MapSize',
            'Seed',
            'C',
            'SR',
            'StartingCredits',
            'O',
        ])->merge(collect($data['Header'])->only([
            'TimeStampBegin',
            'TimeStampEnd',
        ]));

        $players = collect($data['Header']['Metadata']['Players'])->map(function ($player) {
            return collect($player)->except([
                'IP',
                'Port',
                'Unknown',
            ])->all();
        });

        return collect([
            'hash' => $hash,
            'summary' => $summary->all(),
            'header' => $header->all(),
            'meta' => $meta->all(),
            'players' => $players->all(),
        ]);
    }

    private function generateGameHash(Collection $data): ?string
    {
        try {
            $hash = md5(
                $data['Body'][32]['TimeCode'].
                $data['Body'][50]['TimeCode'].
                $data['Header']['Hash'].
                $data['Header']['Metadata']['MapSize'].
                $data['Header']['Metadata']['Seed']
            );
        } catch (\Throwable $th) {
            return null;
        }

        return $hash;
    }

    private function getReplayParserBinary(): string|bool
    {
        // Live parser is proprarity binaries, none-live is dummy binaries
        if (strtoupper(PHP_OS) === 'WINNT') {
            if (Storage::disk('binaries')->exists('replay_parser_live.exe')) {
                return Storage::disk('binaries')->path('replay_parser_live.exe');
            } elseif (Storage::disk('binaries')->exists('replay_parser.exe')) {
                return Storage::disk('binaries')->path('replay_parser.exe');
            }
        } else {
            if (Storage::disk('binaries')->exists('replay_parser_live')) {
                return Storage::disk('binaries')->path('replay_parser_live');
            } elseif (Storage::disk('binaries')->exists('replay_parser')) {
                return Storage::disk('binaries')->path('replay_parser');
            }
        }

        return false;
    }
}
