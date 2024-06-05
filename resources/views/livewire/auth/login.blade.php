<div>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <x-cards.authentication-logo />
            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Sign in to your account
                    </h1>
                    <form wire:submit="login" class="space-y-4 md:space-y-6">
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
                        <div class="flex justify-between">
                            <x-inputs.checkbox wire:model.live="form.remember" for="form.remember">
                                Remember me
                            </x-inputs.checkbox>
                            <x-inputs.error for="form.remember" />
                            <a wire:navigate href="{{ route('password.request') }}"
                                class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">Forgot
                                password?</a>
                        </div>
                        <x-buttons.main class="w-full">Sign in</x-buttons.main>
                        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                            Don’t have an account yet? <a wire:navigate href="{{ route('register') }}"
                                class="font-medium text-blue-600 hover:underline dark:text-blue-500">Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
