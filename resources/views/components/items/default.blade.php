<li class="relative flex justify-between px-4 py-5 gap-x-6 hover:bg-gray-50 sm:px-6 lg:px-8">
    @foreach ($model->getAllowedTableFields() as $field)
        <p>{{ $model->{$field} }}</p>
    @endforeach
</li>
