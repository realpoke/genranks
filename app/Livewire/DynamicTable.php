<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DynamicTable extends Component
{
    use WithPagination;

    public $model;

    public $tableFields = [];

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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

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

    public function render()
    {
        if (count($this->tableFields) <= 0) {
            return view('livewire.dynamic-table', ['data' => []]);
        }

        $query = $this->getModel();

        if ($this->search && method_exists($query, 'scopeSearch')) {
            $query = $query->search(searchTerm: $this->search);
        }

        if ($this->sortBy) {
            $query = $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $data = $query->paginate($this->perPage);

        return view('livewire.dynamic-table', ['data' => $data]);
    }
}
