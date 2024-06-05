<div>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <x-cards.authentication-logo />
            <div
                class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
                <h1
                    class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Confirm your password
                </h1>
                <p class="font-light text-gray-500 dark:text-gray-400">Please confirm your password before continuing!
                </p>

                <form wire:submit.prevent="confirmPassword" class="mt-4 space-y-4 lg:mt-5 md:space-y-5">
                    <div>
                        <x-inputs.label for="form.password">Password</x-inputs.label>
                        <x-inputs.text wire:model="form.password" type="password" name="form.password"
                            id="form.password" />
                        <x-inputs.error for="form.password" />
                    </div>
                    <x-buttons.main class="w-full">Reset password</x-buttons.main>
                </form>
            </div>
        </div>
    </section>
</div>
