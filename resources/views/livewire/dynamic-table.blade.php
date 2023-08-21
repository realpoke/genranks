<div>
    <input wire:model.live="search" type="text" placeholder="Search...">
    <label for="per_page">Per Page:</label>
    <select wire:model.live="perPage" id="per_page">
        <option value="10">10</option>
        <option selected value="20">20</option>
        <option value="30">30</option>
    </select>
    <table>
        <thead>
            <tr>
                @foreach ($tableFields as $field)
                    <th>
                        <div class="text-blue-400" wire:click="orderBy('{{ $field }}')">
                            {{ str_replace('_', ' ', ucwords($field, '_')) }}
                            @if ($sortBy === $field)
                                @if ($sortDirection === 'asc')
                                    <i>up</i>
                                @else
                                    <i>down</i>
                                @endif
                            @endif
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    @foreach ($tableFields as $field)
                        <td>{{ $row[$field] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (count($data) <= 0)
        <p>No data found!</p>
    @endif

    @if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
        {{ $data->links() }}
    @endif
</div>
