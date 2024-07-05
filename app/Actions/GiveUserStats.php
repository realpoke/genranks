<?php

namespace App\Actions;

use App\Contracts\GivesUserStatsContract;
use App\Models\Game;

class GiveUserStats implements GivesUserStatsContract
{
    public function __invoke(Game $game): void
    {
        foreach ($game->users as $user) {
            $pivot = $user->pivot;

            $header = $pivot->header;
            $slot = $header['ArrayReplayOwnerSlot'] ?? null;
            if ($slot === null) {

                continue;
            }

            $summary = $game->summary[$slot] ?? null;
            if ($summary === null) {

                continue;
            }

            // Get the user's stats
            $userStats = $user->stats;

            // Handle the 'Name' field
            $name = $summary['Name'];
            $userStats['Names'] = $userStats['Names'] ?? [];
            if (! in_array($name, $userStats['Names'])) {
                $userStats['Names'][] = $name;
            }

            // Handle the 'Side' field
            $side = $summary['Side'];
            $userStats['Sides'] = $userStats['Sides'] ?? [];
            if (isset($userStats['Sides'][$side])) {
                $userStats['Sides'][$side] += 1;
            } else {
                $userStats['Sides'][$side] = 1;
            }

            // Remove 'Name', 'Side', 'Win', and 'Team' fields from summary as they have been handled
            unset($summary['Name'], $summary['Side'], $summary['Win'], $summary['Team']);

            // Merge the remaining stats
            $userStats = $this->mergeStats($userStats, $summary);

            // Reassign the updated stats back to the user
            $user->stats = $userStats;

            // Save the updated user stats
            $user->save();
        }
    }

    private function mergeStats(array $userStats, array $summary): array
    {
        foreach ($summary as $key => $value) {
            if (is_array($value) && isset($userStats[$key]) && is_array($userStats[$key])) {
                $userStats[$key] = $this->mergeStats($userStats[$key], $value);
            } elseif (is_numeric($value) && isset($userStats[$key]) && is_numeric($userStats[$key])) {
                $userStats[$key] += $value;
            } else {
                $userStats[$key] = $value;
            }
        }

        return $userStats;
    }
}
