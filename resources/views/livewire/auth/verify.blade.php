<div>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <x-cards.authentication-logo />
            <div
                class="w-full p-6 bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md dark:bg-gray-800 dark:border-gray-700 sm:p-8">
                <h1
                    class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                    Verify your email address
                </h1>
                <p class="font-light text-gray-500 dark:text-gray-400">Or <a wire:click.prevent="logout"
                        class="font-medium text-blue-600 cursor-pointer hover:underline dark:text-blue-500">sign
                        out</a>!
                </p>

                <div style="display: none;" x-data="{ show: false }" x-show="show" x-on:email-resent.window="show=true"
                    class="flex items-center px-4 py-3 mb-6 space-x-4 text-sm text-green-800 bg-green-500 rounded shadow"
                    role="alert">
                    <x-icons icon="check-circle" />

                    <p>A fresh verification link has been sent to your email address.</p>
                </div>

                <div class="text-sm text-gray-700">
                    <p>Before proceeding, please check your email for a verification link.</p>

                    <p class="mt-3">
                        If you did not receive the email, <a wire:click="resend"
                            class="font-medium text-blue-600 cursor-pointer hover:underline dark:text-blue-500">click
                            here to request another</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
