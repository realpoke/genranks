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
        } catch (\Error $e) {
            if (strpos($e->getMessage(), 'Allowed memory size of') !== false) {
                Log::error('Memory limit exceeded while decoding json for '.$replay.': '.$e->getMessage());

                return collect();
            }
            throw $e; // Re-throw if it's not a memory limit error
        }

        $gameHash = $this->generateGameHash($decodedData);

        if (is_null($gameHash)) {
            // TODO: This seems to fail too often, make it better
            Log::error('Failed to generate game hash');

            return collect();
        }

        $decodedData = $this->removeObservers($decodedData);

        if ($decodedData->isEmpty()) {
            Log::info('Skipping replay parsing as the uploader was an observer.');

            return collect();
        }

        $decodedData = $this->findSurrenderOrder($decodedData);

        return $this->cleanData($decodedData, $gameHash);
    }

    // Remove observers from summary and Header->Metadata->Players
    // Then reassign slot numbers and adjust ReplayOwnerSlot
    private function removeObservers(Collection $data): Collection
    {
        // Check if the replay uploader is an observer
        $replayOwnerSlot = ($data['Header']['ReplayOwnerSlot'] - 3000) / 100;

        if (isset($data['Header']['Metadata']['Players'][$replayOwnerSlot])) {
            $uploaderPlayer = $data['Header']['Metadata']['Players'][$replayOwnerSlot];
            if ($uploaderPlayer['Faction'] == '-2') {
                Log::warning('Replay uploader is an observer. Skipping parsing.');

                return collect();
            }
        } else {
            Log::warning('ReplayOwnerSlot is out of range. Skipping parsing.');

            return collect();
        }

        $headerPlayers = collect($data['Header']['Metadata']['Players']);
        $filteredHeaderPlayers = $headerPlayers->filter(function ($player) {
            return $player['Faction'] != '-2';
        });

        $summary = collect($data['Summary']);
        $filteredSummary = $summary->filter(function ($summaryPlayer) use ($filteredHeaderPlayers) {
            return $filteredHeaderPlayers->contains(function ($headerPlayer) use ($summaryPlayer) {
                return $summaryPlayer['Name'] == $headerPlayer['Name'];
            });
        });

        // Reassign slot numbers and adjust ReplayOwnerSlot
        $newReplayOwnerSlot = null;
        $filteredHeaderPlayers = $filteredHeaderPlayers->values()->map(function ($player, $index) use ($uploaderPlayer, &$newReplayOwnerSlot) {
            $newSlot = 3000 + ($index * 100);
            if ($player['Name'] == $uploaderPlayer['Name']) {
                $newReplayOwnerSlot = $newSlot;
            }

            return $player;
        });

        // Update the data with the filtered collections
        $data->put('Header', array_merge($data['Header'], [
            'Metadata' => array_merge($data['Header']['Metadata'], [
                'Players' => $filteredHeaderPlayers->toArray(),
            ]),
            'ReplayOwnerSlot' => $newReplayOwnerSlot ?? $data['Header']['ReplayOwnerSlot'],
        ]));
        $data->put('Summary', $filteredSummary->values()->toArray());

        return $data;
    }

    private function findSurrenderOrder(Collection $data): Collection
    {
        // Extract the 'Win' attribute from each player and get unique values
        $winValues = collect($data['Summary'])->pluck('Win')->unique();
        foreach (collect($data['Summary']) as $player) {
            $winStatus = $player['Win'] ? 'true' : 'false';
        }

        // Check if there is only one unique value in the 'Win' attributes
        if ($winValues->count() !== 1) {

            return $data;
        }

        $surrenderedPlayer = null;

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

        $mapHasher = new CreateMapHash();

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
        ]))->merge(['MapHash' => $mapHasher(
            collect(explode('/', $data['Header']['Metadata']['MapFile']))->pop(),
            $data['Header']['Metadata']['MapCRC'],
            $data['Header']['Metadata']['MapSize'],
        )]);

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

    // TODO: Sometimes the game hash for the same replay but diffrent uploader is not the same. Fix this.
    private function generateGameHash(Collection $data): ?string
    {
        try {
            $hash = md5(
                $data['Body'][5]['TimeCode'].
                $data['Body'][10]['OrderCode'].
                $data['Header']['Hash'].
                $data['Header']['Metadata']['MapSize'].
                $data['Header']['Metadata']['Seed']
            );
        } catch (\Throwable $th) {

            Log::debug('Failed to generate game hash');
            Log::debug('Body: '.collect($data['Body']));
            Log::debug('TimeCode: '.(isset($data['Body'][5]) && isset($data['Body'][5]['TimeCode']) ? $data['Body'][5]['TimeCode'] : 'TimeCode not found'));
            Log::debug('OrderCode: '.(isset($data['Body'][10]) && isset($data['Body'][10]['OrderCode']) ? $data['Body'][10]['OrderCode'] : 'OrderCode not found'));
            Log::debug('Hash: '.$data['Header']['Hash']);
            Log::debug('MapSize: '.$data['Header']['Metadata']['MapSize']);
            Log::debug('Seed: '.$data['Header']['Metadata']['Seed']);

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
