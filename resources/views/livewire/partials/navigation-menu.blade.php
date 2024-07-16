<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:bg-gray-800 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-4 sm:flex">
                    <x-navbars.nav-link wire:navigate href="{{ route('home') }}" :active="request()->routeIs('home')">
                        {{ __('Welcome') }}
                    </x-navbars.nav-link>
                    <x-navbars.nav-link wire:navigate href="{{ route('leaderboard.index') }}" :active="request()->routeIs('leaderboard.*')">
                        {{ __('Leaderboard') }}
                    </x-navbars.nav-link>
                    <x-navbars.nav-link wire:navigate href="{{ route('game.index') }}" :active="request()->routeIs('game.*')">
                        {{ __('Games') }}
                    </x-navbars.nav-link>
                    <x-navbars.nav-link wire:navigate href="{{ route('map.index') }}" :active="request()->routeIs('map.*')">
                        {{ __('Maps') }}
                    </x-navbars.nav-link>
                </div>
            </div>

            <div class="hidden space-x-4 sm:flex sm:items-center sm:ml-6">
                <x-buttons.link download class="inline-flex" target="_blank" href="{{ route('genlink.download') }}">
                    GenLink
                    <x-icons class="ml-2" icon="download" />
                </x-buttons.link>
                @auth
                    <!-- Options Dropdown -->
                    <div class="relative ml-3">
                        <x-menus.dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <x-buttons.invisible class="inline-flex">
                                        {{ $name }}

                                        <x-icons icon='chevron-down' class="ml-1" />
                                    </x-buttons.invisible>
                                </span>
                            </x-slot>


                            <x-slot name="content">
                                <x-menus.dropdown-link wire:navigate href="{{ Auth::user()->route() }}">
                                    {{ __('Profile') }}
                                </x-menus.dropdown-link>

                                @if (Auth::user()->hasClan())
                                    <x-menus.dropdown-link wire:navigate
                                        href="{{ route('clan.show', Auth::user()->myClan()) }}">
                                        {{ __('My Clan') }}
                                    </x-menus.dropdown-link>
                                @endif

                                @can('viewAny:filament')
                                    <x-menus.dropdown-link href="{{ route('filament.admin.pages.dashboard') }}">
                                        {{ __('Admin') }}
                                    </x-menus.dropdown-link>
                                @endcan

                                <!-- Account Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Account') }}
                                </div>

                                <x-menus.dropdown-link wire:navigate href="{{ route('options.create') }}">
                                    {{ __('Options') }}
                                </x-menus.dropdown-link>

                                <x-menus.dropdown-link wire:navigate href="{{ route('clan.settings') }}">
                                    {{ __('Clan') }}
                                </x-menus.dropdown-link>

                                <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                <!-- Authentication -->
                                <x-menus.dropdown-link class="cursor-pointer" wire:click="logout">
                                    {{ __('Log Out') }}
                                </x-menus.dropdown-link>
                            </x-slot>
                        </x-menus.dropdown>
                    </div>
                @else
                    <x-buttons.invisible-link class="inline-flex" wire:navigate href="{{ route('login') }}">
                        Log in
                        <x-icons class="ml-2" icon="log-in" />
                    </x-buttons.invisible-link>

                    <x-buttons.invisible-link class="inline-flex" wire:navigate href="{{ route('register') }}">
                        Register
                        <x-icons class="ml-2" icon="user-plus" />
                    </x-buttons.invisible-link>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="flex items-center -mr-2 sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">

            <x-navbars.responsive-nav-link download class="inline-flex" target="_blank"
                href="{{ route('genlink.download') }}">
                GenLink
                <x-icons class="ml-2" icon="download" />
            </x-navbars.responsive-nav-link>
            <x-navbars.responsive-nav-link wire:navigate href="{{ route('home') }}" :active="request()->routeIs('home')">
                {{ __('Welcome') }}
            </x-navbars.responsive-nav-link>
            <x-navbars.responsive-nav-link wire:navigate href="{{ route('leaderboard.index') }}" :active="request()->routeIs('leaderboard.*')">
                {{ __('Leaderboard') }}
            </x-navbars.responsive-nav-link>
            <x-navbars.responsive-nav-link wire:navigate href="{{ route('game.index') }}" :active="request()->routeIs('game.*')">
                {{ __('Games') }}
            </x-navbars.responsive-nav-link>
            <x-navbars.responsive-nav-link wire:navigate href="{{ route('map.index') }}" :active="request()->routeIs('map.*')">
                {{ __('Maps') }}
            </x-navbars.responsive-nav-link>
        </div>

        <!-- Responsive Options Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="flex items-center justify-between px-4">
                    <div>
                        <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ $name }}
                        </div>
                        <div class="text-sm font-medium text-gray-500">{{ $email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-navbars.responsive-nav-link wire:navigate href="{{ Auth::user()->route() }}" :active="request()->routeIs(Auth::user()->route())">
                        {{ __('Profile') }}
                    </x-navbars.responsive-nav-link>

                    @if (request()->user()->hasClan())
                        <x-navbars.responsive-nav-link wire:navigate
                            href="{{ route('clan.show', request()->user()->myClan()) }}" :active="request()->routeIs('clan.show', request()->user()->myClan())">
                            {{ __('My Clan') }}
                        </x-navbars.responsive-nav-link>
                    @endif

                    @can('viewAny:filament')
                        <x-navbars.responsive-nav-link href="{{ route('filament.admin.pages.dashboard') }}">
                            {{ __('Admin') }}
                        </x-navbars.responsive-nav-link>
                    @endcan

                    <!-- Account Management -->
                    <x-navbars.responsive-nav-link wire:navigate href="{{ route('options.create') }}" :active="request()->routeIs('options.create')">
                        {{ __('Options') }}
                    </x-navbars.responsive-nav-link>

                    <x-navbars.responsive-nav-link wire:navigate href="{{ route('clan.settings') }}" :active="request()->routeIs('clan.settings')">
                        {{ __('Clan') }}
                    </x-navbars.responsive-nav-link>

                    <!-- Authentication -->
                    <x-navbars.responsive-nav-link class="cursor-pointer" wire:click="logout">
                        {{ __('Log Out') }}
                    </x-navbars.responsive-nav-link>
                </div>
            @else
                <div class="space-y-1">
                    <x-navbars.responsive-nav-link wire:navigate href="{{ route('login') }}" :active="request()->routeIs('login')">
                        {{ __('Login') }}
                    </x-navbars.responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
