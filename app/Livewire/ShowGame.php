<?php

namespace App\Livewire;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShowGame extends Component
{
    public Game $game;

    public array $categories;

    public Collection $users;

    public array $comparisonData = [];

    public $time;

    public function mount(Game $game)
    {
        $this->game = $game;
        $this->users = $game->users()->get();
    }

    public function render()
    {
        $this->categories = ['UnitsCreated', 'BuildingsBuilt', 'UpgradesBuilt', 'PowersUsed'];
        $this->comparisonData = $this->computeComparisonData($this->categories);
        $begin = Carbon::createFromTimestamp($this->game->meta['TimeStampBegin']);
        $end = Carbon::createFromTimestamp($this->game->meta['TimeStampEnd']);

        // Calculate the difference
        $this->time = $begin->diffForHumans($end, true);

        return view('livewire.show-game');
    }

    #[Computed()]
    private function computeComparisonData(array $categories): array
    {
        $comparisonData = [];

        foreach ($categories as $category) {
            $firstData = $this->game->summary[0][$category];
            $secondData = $this->game->summary[1][$category];

            // Ensure $firstData and $secondData are arrays
            if (! is_array($firstData) || ! is_array($secondData)) {
                continue; // Skip if not array (or handle differently based on your logic)
            }

            $firstCollection = collect($firstData);
            $secondCollection = collect($secondData);

            // Summing 'TotalSpent' and 'Count' if available, otherwise summing integers directly
            $firstTotalSpent = $firstCollection->sum(function ($item) {
                return is_array($item) && isset($item['TotalSpent']) ? $item['TotalSpent'] : 0;
            });
            $firstTotalCount = $firstCollection->sum(function ($item) {
                return is_array($item) && isset($item['Count']) ? $item['Count'] : $item;
            });

            $secondTotalSpent = $secondCollection->sum(function ($item) {
                return is_array($item) && isset($item['TotalSpent']) ? $item['TotalSpent'] : 0;
            });
            $secondTotalCount = $secondCollection->sum(function ($item) {
                return is_array($item) && isset($item['Count']) ? $item['Count'] : $item;
            });

            $spentDiff = $secondTotalSpent - $firstTotalSpent;
            $countDiff = $secondTotalCount - $firstTotalCount;

            $totalSpentPercentage = $firstTotalSpent > 0 ? ($spentDiff / $firstTotalSpent) * 100 : 0;
            $totalCountPercentage = $firstTotalCount > 0 ? ($countDiff / $firstTotalCount) * 100 : 0;

            $comparisonData[$category] = [
                'firstTotalSpent' => $firstTotalSpent,
                'secondTotalSpent' => $secondTotalSpent,
                'totalSpentPercentage' => $totalSpentPercentage,
                'firstTotalCount' => $firstTotalCount,
                'secondTotalCount' => $secondTotalCount,
                'totalCountPercentage' => $totalCountPercentage,
            ];
        }

        return $comparisonData;
    }
}
