<div>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <x-cards.authentication-logo />
            <div
                class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
                <h1
                    class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Forgot your password?
                </h1>
                <p class="font-light text-gray-500 dark:text-gray-400">Don't fret! Just type in your email and we will
                    send you a code to reset your password!</p>

                @if ($form->emailSentMessage != null)
                    <div class="p-4 text-green-800 rounded-md bg-green-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-icons icon="check-circle" />
                            </div>

                            <div class="ml-3">
                                <p class="text-sm font-medium leading-5">
                                    {{ $form->emailSentMessage }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <form wire:submit.prevent="sendResetPasswordLink" class="mt-4 space-y-4 lg:mt-5 md:space-y-5">
                        <div>
                            <x-inputs.label for="form.email">Your email</x-inputs.label>
                            <x-inputs.text wire:model="form.email" name="form.email" id="form.email" />
                            <x-inputs.error for="form.email" />
                        </div>
                        <div>
                            <x-inputs.checkbox wire:model.live="form.terms" for="form.terms">
                                I accept the <a class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                    wire:navigate href="{{ route('markdown.show', 'service') }}">Terms and
                                    Conditions</a>
                            </x-inputs.checkbox>
                            <x-inputs.error for="form.terms" />
                        </div>
                        <x-buttons.main class="w-full">Reset password</x-buttons.main>
                    </form>
                @endif
            </div>
        </div>
    </section>
</div>
