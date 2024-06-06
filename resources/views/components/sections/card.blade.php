<div {{ $attributes->merge(['class' => 'mt-5 md:mt-0 md:col-span-2']) }}>
    <div class="px-4 py-5 text-gray-600 bg-white shadow sm:p-6 dark:bg-gray-800 sm:rounded-lg dark:text-gray-400">
        {{ $slot }}
    </div>
</div>
