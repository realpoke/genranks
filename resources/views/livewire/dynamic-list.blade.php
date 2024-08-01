<div>
    <div>
        <input wire:model.live="search" type="text" placeholder="Search...">
        <label for="per_page">Per Page:</label>
        <select wire:model.live="perPage" id="per_page">
            <option value="10">10</option>
            <option selected value="20">20</option>
            <option value="30">30</option>
        </select>
        @if ($extraFiltersView)
            <x-dynamic-component :component="$extraFiltersView" />
        @endif
    </div>
    <table class="w-full">
        <thead>
            <tr class="justify-between">
                @foreach ($listFields as $field)
                    <th class="text-blue-400" wire:click="orderBy('{{ $field }}')">
                        {{ str_replace('_', ' ', ucwords($field, '_')) }}
                        @if ($sortBy === $field)
                            @if ($sortDirection === 'asc')
                                <i>up</i>
                            @else
                                <i>down</i>
                            @endif
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
    </table>

    <ul role="list" class="divide-y divide-gray-100">
        @foreach ($data as $model)
            <x-dynamic-component :component="$itemView" :model="$model" />
        @endforeach
    </ul>

    @if (count($data) <= 0)
        <p>No data found!</p>
    @endif

    @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
        {{ $data->links() }}
    @endif
</div>
