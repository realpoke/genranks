<li class="flex items-center justify-between py-4">
    <span>
        {{ $model->ranked ? 'Ranked' : 'Unranked' }} - {{ $model->name }} ({{ $model->hash }}) -
        {{ $model->type }}
    </span>
    @if ($model->file)
        <button wire:click="downloadMap({{ $model->id }})"
            class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
            Download
        </button>
    @endif
</li>
