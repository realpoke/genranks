<?php

namespace App\Actions;

use App\Contracts\GivesUserStatsContract;
use App\Models\Game;
use Illuminate\Support\Facades\Log;

class GiveUserStats implements GivesUserStatsContract
{
    public function __invoke(Game $game): void
    {
        Log::debug('Updating user stats for game: '.$game->id);
        foreach ($game->users as $user) {
            $pivot = $user->pivot;
            Log::debug('Pivot: '.collect($pivot));

            $header = $pivot->header;
            $slot = $header['ArrayReplayOwnerSlot'] ?? null;
            Log::debug('Slot: '.$slot);
            if ($slot === null) {
                Log::debug('Skipping user '.$user->id.' as no slot was found in the header');

                continue;
            }

            $summary = $game->summary[$slot] ?? null;
            Log::debug('Summary: '.collect($summary));
            if ($summary === null) {
                Log::debug('Skipping user '.$user->id.' as no summary was found for slot '.$slot);

                continue;
            }

            // Get the user's stats
            $userStats = $user->stats;
            Log::debug('User stats: '.collect($userStats));

            // Handle the 'Name' field
            $name = $summary['Name'];
            Log::debug('Name: '.$name);
            $userStats['Names'] = $userStats['Names'] ?? [];
            Log::debug('Names: '.collect($userStats['Names']));
            if (! in_array($name, $userStats['Names'])) {
                Log::debug('Adding name '.$name.' to user stats');
                $userStats['Names'][] = $name;
            }

            // Handle the 'Side' field
            $side = $summary['Side'];
            Log::debug('Side: '.$side);
            $userStats['Sides'] = $userStats['Sides'] ?? [];
            Log::debug('Sides: '.collect($userStats['Sides']));
            if (isset($userStats['Sides'][$side])) {
                Log::debug('Updating side '.$side.' to '.($userStats['Sides'][$side] + 1));
                $userStats['Sides'][$side] += 1;
            } else {
                Log::debug('Adding side '.$side.' to user stats');
                $userStats['Sides'][$side] = 1;
            }

            // Remove 'Name', 'Side', 'Win', and 'Team' fields from summary as they have been handled
            unset($summary['Name'], $summary['Side'], $summary['Win'], $summary['Team']);

            // Merge the remaining stats
            $userStats = $this->mergeStats($userStats, $summary);

            // Reassign the updated stats back to the user
            Log::debug('Updating user stats');
            $user->stats = $userStats;

            // Save the updated user stats
            Log::debug('Saving user stats');
            $user->save();
        }
    }

    private function mergeStats(array $userStats, array $summary): array
    {
        Log::debug('Merging stats');
        foreach ($summary as $key => $value) {
            Log::debug('Key: '.$key);
            if (is_array($value) && isset($userStats[$key]) && is_array($userStats[$key])) {
                Log::debug('Merging array');
                $userStats[$key] = $this->mergeStats($userStats[$key], $value);
            } elseif (is_numeric($value) && isset($userStats[$key]) && is_numeric($userStats[$key])) {
                Log::debug('Merging numeric');
                $userStats[$key] += $value;
            } else {
                Log::debug('Merging other');
                $userStats[$key] = $value;
            }
        }

        Log::debug('Returning merged stats');

        return $userStats;
    }
}
