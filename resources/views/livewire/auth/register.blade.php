<div>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <x-cards.authentication-logo />
            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Create and account
                    </h1>
                    <form wire:submit="register" class="space-y-4 md:space-y-6">
                        <div>
                            <x-inputs.label for="form.name">Your name</x-inputs.label>
                            <x-inputs.text wire:model.blur="form.name" name="form.name" id="form.name" />
                            <x-inputs.error for="form.name" />
                        </div>
                        <div>
                            <x-inputs.label for="form.email">Your email</x-inputs.label>
                            <x-inputs.text wire:model="form.email" name="form.email" id="form.email" />
                            <x-inputs.error for="form.email" />
                        </div>
                        <div>
                            <x-inputs.label for="form.password">Password</x-inputs.label>
                            <x-inputs.text wire:model="form.password" type="password" name="form.password"
                                id="form.password" />
                            <x-inputs.error for="form.password" />
                        </div>
                        <div>
                            <x-inputs.label for="form.password_confirmation">Password Confirmation</x-inputs.label>
                            <x-inputs.text wire:model.blur="form.password_confirmation" type="password"
                                name="form.password_confirmation" id="form.password_confirmation" />
                            <x-inputs.error for="form.password_confirmation" />
                        </div>
                        <div>
                            <x-inputs.checkbox wire:model.live="form.terms" for="form.terms">
                                I accept the <a class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                    wire:navigate href="{{ route('markdown.show', 'service') }}">Terms and
                                    Conditions</a>
                            </x-inputs.checkbox>
                            <x-inputs.error for="form.terms" />
                        </div>
                        <x-buttons.main class="w-full">Create an account</x-buttons.main>
                        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                            Already have an account? <a wire:navigate href="{{ route('login') }}"
                                class="font-medium text-blue-600 hover:underline dark:text-blue-500">Login
                                here</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
