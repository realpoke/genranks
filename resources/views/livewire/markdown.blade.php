<div>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-12 mx-auto md:h-screen lg:py-0">
            <div class="mt-12">
                <a href="{{ route('home') }}"
                    class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
                    <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg"
                        alt="logo">
                    Flowbite
                </a>
            </div>

            <div
                class="w-full p-6 mt-6 mb-12 overflow-auto prose bg-white shadow-md sm:max-w-2xl dark:bg-gray-800 sm:rounded-lg dark:prose-invert">
                {!! $markdown !!}
            </div>
        </div>
    </section>
</div>
