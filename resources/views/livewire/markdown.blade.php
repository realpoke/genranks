<div>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-12 mx-auto md:h-screen lg:py-0">
            <div class="mt-12">
                <x-cards.authentication-logo />
            </div>

            <div
                class="w-full p-6 mt-6 mb-12 overflow-auto prose bg-white shadow-md sm:max-w-2xl dark:bg-gray-800 sm:rounded-lg dark:prose-invert">
                {!! $markdown !!}
            </div>
        </div>
    </section>
</div>
