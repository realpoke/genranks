<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DynamicTable extends Component
{
    use WithPagination;

    public $model;

    public $tableFields = [];

    private $filters = [];

    protected $viewName = 'livewire.dynamic-table';

    #[Url]
    public $sortBy = '';

    #[Url]
    public $sortDirection = 'asc';

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 20;

    public function mount(string $model)
    {
        $this->model = $model;
        $this->tableFields = $this->getModel()::$allowedTableFields ?? [];
    }

    private function setupFilters(): void
    {
        $this->setupDefaultFilters();
        $this->setupExtraFilters();
    }

    private function setupDefaultFilters(): void
    {
        $this->addFilter(function ($query) {
            return $this->applySearch($query);
        });
        $this->addFilter(function ($query) {
            return $this->applySorting($query);
        });
    }

    protected function setupExtraFilters(): void
    {
        // This method can be overridden by child components
        // To add more filters
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[Computed()]
    private function getModel(): Model
    {
        return new $this->model;
    }

    public function orderBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    private function addFilter(callable $filter): void
    {
        $this->filters[] = $filter;
    }

    private function applySearch(Model|Builder $query): Model|Builder
    {
        if ($this->search && method_exists($query, 'scopeSearch')) {
            return $query->search(searchTerm: $this->search);
        }

        return $query;
    }

    private function applySorting(Model|Builder $query): Model|Builder
    {
        if ($this->sortBy) {
            return $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query;
    }

    private function applyFilters(Model $query): Model|Builder
    {
        $this->setupFilters();
        foreach ($this->filters as $filter) {
            $query = $filter($query);
        }

        return $query;
    }

    public function render()
    {
        if (count($this->tableFields) <= 0) {
            return view($this->viewName, ['data' => []]);
        }

        $model = $this->getModel();

        $query = $this->applyFilters($model);

        $data = $query->paginate($this->perPage);

        return view($this->viewName, ['data' => $data]);
    }
}
