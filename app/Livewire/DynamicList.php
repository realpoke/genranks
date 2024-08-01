<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DynamicList extends Component
{
    use WithPagination;

    public Model $model;

    public $listFields = [];

    private $filters = [];

    // Override these properties in child components to change the view name and row view
    protected $viewName = 'livewire.dynamic-list';

    protected $itemView = 'items.default';

    protected $extraFiltersView = null;

    #[Url]
    public $sortBy = '';

    #[Url]
    public $sortDirection = 'asc';

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 20;

    public array $passthrough;

    public function mount(array $passthrough = [])
    {
        $this->passthrough = $passthrough;
        $this->model = $this->getModel();
        $this->listFields = method_exists($this->model, 'getAllowedListFields') ? $this->model::getAllowedListFields() : [];
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
        // Override this method in child components to add extra filters
        // Example:
        // $this->addFilter(function ($query) {
        //     return $query->where('some_column', 'some_value');
        // });
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    // This method must be overridden in child components to return the model
    #[Computed()]
    protected function getModel(): Model
    {
        throw new \LogicException(sprintf(
            'The %s::getModel() method must be overridden in child components',
            static::class
        ));
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

    public function addFilter(callable $filter): void
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
        if (count($this->listFields) <= 0) {
            return view($this->viewName, ['data' => []]);
        }

        $model = $this->model;

        $query = $this->applyFilters($model);

        $data = $query->paginate($this->perPage);

        return view($this->viewName, [
            'data' => $data,
            'itemView' => $this->itemView,
            'extraFiltersView' => $this->extraFiltersView,
        ]);
    }
}
