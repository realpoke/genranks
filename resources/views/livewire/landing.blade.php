<div>
    <div class="overflow-hidden">
        <div class="px-6 pb-32 mx-auto max-w-7xl pt-36 sm:pt-60 lg:px-8 lg:pt-32">
            <div class="max-w-2xl mx-auto gap-x-14 lg:mx-0 lg:flex lg:max-w-none lg:items-center">
                <div class="relative w-full max-w-xl lg:shrink-0 xl:max-w-2xl">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Dominate the Battlefield:
                        GenRanks</h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 sm:max-w-md lg:max-w-none">Compete with the best and
                        climb
                        the ranks in Command & Conquer Generals Zero Hour. Showcase your strategic prowess, track your
                        progress, and become the ultimate general in this iconic real-time strategy game.</p>
                    <div class="flex items-center mt-10 gap-x-6">
                        @guest
                            <a href="{{ route('register') }}" wire:navigate
                                class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Get
                                started</a>
                        @else
                            <a href="{{ request()->user()->route() }}" wire:navigate
                                class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Your
                                profile</a>
                        @endguest
                        <a href="{{ route('leaderboard.index') }}" wire:navigate
                            class="text-sm font-semibold leading-6 text-gray-900">Leaderboard <span
                                aria-hidden="true">→</span></a>
                    </div>
                </div>
                <div class="flex justify-end gap-8 mt-14 sm:-mt-44 sm:justify-start sm:pl-20 lg:mt-0 lg:pl-0">
                    <div
                        class="flex-none pt-32 ml-auto space-y-8 w-44 sm:ml-0 sm:pt-80 lg:order-last lg:pt-36 xl:order-none xl:pt-80">
                        <div class="relative">
                            <img src="{{ asset('images/landing/1.jpg') }}" alt=""
                                class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg">
                            <div
                                class="absolute inset-0 pointer-events-none rounded-xl ring-1 ring-inset ring-gray-900/10">
                            </div>
                        </div>
                    </div>
                    <div class="flex-none mr-auto space-y-8 w-44 sm:mr-0 sm:pt-52 lg:pt-36">
                        <div class="relative">
                            <img src="{{ asset('images/landing/2.jpg') }}" alt=""
                                class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg">
                            <div
                                class="absolute inset-0 pointer-events-none rounded-xl ring-1 ring-inset ring-gray-900/10">
                            </div>
                        </div>
                        <div class="relative">
                            <img src="{{ asset('images/landing/3.jpg') }}" alt=""
                                class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg">
                            <div
                                class="absolute inset-0 pointer-events-none rounded-xl ring-1 ring-inset ring-gray-900/10">
                            </div>
                        </div>
                    </div>
                    <div class="flex-none pt-32 space-y-8 w-44 sm:pt-0">
                        <div class="relative">
                            <img src="{{ asset('images/landing/4.jpg') }}" alt=""
                                class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg">
                            <div
                                class="absolute inset-0 pointer-events-none rounded-xl ring-1 ring-inset ring-gray-900/10">
                            </div>
                        </div>
                        <div class="relative">
                            <img src="{{ asset('images/landing/5.jpg') }}" alt=""
                                class="aspect-[2/3] w-full rounded-xl bg-gray-900/5 object-cover shadow-lg">
                            <div
                                class="absolute inset-0 pointer-events-none rounded-xl ring-1 ring-inset ring-gray-900/10">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="relative py-24 overflow-hidden bg-gray-900 isolate sm:py-32">
        <img src="{{ asset('images/landing/6.jpg') }}" alt=""
            class="absolute inset-0 object-cover object-right w-full h-full -z-10 md:object-center">
        <div class="hidden sm:absolute sm:-top-10 sm:right-1/2 sm:-z-10 sm:mr-10 sm:block sm:transform-gpu sm:blur-3xl">
            <div class="aspect-[1097/845] w-[68.5625rem] bg-gradient-to-tr from-[#ff4694] to-[#776fff] opacity-20"
                style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
            </div>
        </div>
        <div
            class="absolute -top-52 left-1/2 -z-10 -translate-x-1/2 transform-gpu blur-3xl sm:top-[-28rem] sm:ml-16 sm:translate-x-0 sm:transform-gpu">
            <div class="aspect-[1097/845] w-[68.5625rem] bg-gradient-to-tr from-[#ff4694] to-[#776fff] opacity-20"
                style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)">
            </div>
        </div>
        <div class="px-6 mx-auto max-w-7xl lg:px-8">
            <div class="max-w-2xl mx-auto lg:mx-0">
                <h2 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">Top Commanders</h2>
                <p class="mt-6 text-lg leading-8 text-gray-300">Meet the best players in C&C Generals Zero Hour. These
                    top commanders have demonstrated exceptional strategy and skill to reach the pinnacle of the
                    leaderboard.</p>
            </div>
            <div
                class="grid max-w-2xl grid-cols-1 gap-6 mx-auto mt-16 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3 lg:gap-8">


                @forelse ($topCommanders as $commander)
                    <div class="flex p-6 gap-x-4 rounded-xl bg-white/5 ring-1 ring-inset ring-white/10">
                        <div class="text-base leading-7">
                            <h3 class="font-semibold text-white">{{ $commander->name }} - {{ $commander->rank }}</h3>
                            <p class="mt-2 text-gray-300">{{ $commander->elo }}</p>
                        </div>
                    </div>
                @empty
                    <div class="flex p-6 gap-x-4 rounded-xl bg-white/5 ring-1 ring-inset ring-white/10">
                        <div class="text-base leading-7">
                            <h3 class="font-semibold text-white">This could be you!</h3>
                            <a href="{{ route('register') }}" wire:navigate
                                class="mt-2 text-indigo-600 hover:underline"> Register now</a>
                        </div>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
    <div class="py-32 bg-white">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">
            <div class="max-w-2xl mx-auto lg:mx-0 lg:max-w-none">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Our mission</h2>
                <div class="flex flex-col mt-6 gap-x-8 gap-y-20 lg:flex-row">
                    <div class="lg:w-full lg:max-w-2xl lg:flex-auto">
                        <p class="text-xl leading-8 text-gray-600">At GenRanks, our mission is
                            to create a vibrant and competitive community for all fans of the game. We aim to provide a
                            fair and transparent platform where players can showcase their skills, track their progress,
                            and strive for the top spot. Our commitment is to ensure that every player has an equal
                            opportunity to compete and be recognized for their achievements.</p>
                        <p class="max-w-xl mt-10 text-base leading-7 text-gray-700">Through regular updates, community
                            events, and dedicated support, we aspire to foster a sense of camaraderie and sportsmanship
                            among players. Our goal is not just to highlight the best players but to encourage growth
                            and improvement for all participants. Join us in our mission to elevate the gaming
                            experience and make C&C Generals Zero Hour a beloved competitive platform for years to come.
                        </p>
                    </div>
                    <div class="lg:flex lg:flex-auto lg:justify-center">
                        <dl class="w-64 space-y-8 xl:w-80">
                            <div class="flex flex-col-reverse gap-y-4">
                                <dt class="text-base leading-7 text-gray-600">Games processed</dt>
                                <dd class="text-5xl font-semibold tracking-tight text-gray-900">
                                    {{ number_format($gamesProcessed) }}
                                </dd>
                            </div>
                            <div class="flex flex-col-reverse gap-y-4">
                                <dt class="text-base leading-7 text-gray-600">Active users</dt>
                                <dd class="text-5xl font-semibold tracking-tight text-gray-900">
                                    {{ number_format($activeUsers) }}
                                </dd>
                            </div>
                            <div class="flex flex-col-reverse gap-y-4">
                                <dt class="text-base leading-7 text-gray-600">Elo exchanged last 24 hours</dt>
                                <dd class="text-5xl font-semibold tracking-tight text-gray-900">
                                    {{ number_format($eloChanged) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white">

        <div class="bg-gray-900">
            <div class="px-6 py-16 mx-auto max-w-7xl sm:py-24 lg:px-8">
                <h2 class="text-2xl font-bold leading-10 tracking-tight text-white">Frequently asked questions</h2>
                <p class="max-w-2xl mt-6 text-base leading-7 text-gray-300">Have a different question and can’t find the
                    answer you’re looking for? Reach out to our support team on <a target="_blank"
                        href="{{ route('discord') }}"
                        class="font-semibold text-indigo-400 hover:text-indigo-300">Discord</a>, and we'll respond as
                    quickly as possible.</p>
                <div class="mt-20">
                    <dl class="space-y-16 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:gap-y-16 sm:space-y-0 lg:gap-x-10">
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">What is C&C Generals Zero Hour?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">C&C Generals Zero Hour is a real-time
                                strategy game developed by Electronic Arts. It's an expansion pack for Command &
                                Conquer: Generals and is known for its strategic gameplay and multiplayer modes.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">How do I participate in the
                                leaderboard?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">To join the leaderboard, you need to
                                create an account on our website and link your profile using GenLink. Once registered,
                                your game
                                statistics will automatically be updated in real-time.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">Is there a fee to participate in
                                the leaderboard?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">No, participation in our leaderboard is
                                completely free. We believe in providing an open platform for all players to compete and
                                showcase their skills.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">How often are the leaderboard
                                stats updated?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">The leaderboard stats are updated in
                                real-time. Whenever you complete a game session, your stats will be reflected
                                immediately on our leaderboard.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">What criteria are used to rank
                                players on the leaderboard?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">Players are ranked based on various
                                factors such as win/loss ratio, total matches played, and overall score. The exact
                                formula may vary and is designed to ensure fairness and competitiveness.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">Can I see my past game history on
                                the leaderboard?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">Yes, you can view your detailed game
                                history including match results, scores, and opponent details by accessing your profile
                                on our website.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">How can I report a bug or issue
                                with the leaderboard?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">If you encounter any issues or bugs
                                while using the leaderboard, please contact our support team via our <a target="_blank"
                                    href="{{ route('discord') }}"
                                    class="font-semibold text-indigo-400 hover:text-indigo-300">Discord</a>. Provide as
                                much detail as possible to help us
                                resolve the issue promptly.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">Are there prizes for top-ranking
                                players on the leaderboard?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">Currently, we do not offer prizes for
                                leaderboard rankings. Our focus is on fostering a competitive community and recognizing
                                top-performing players.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">Can I participate in tournaments
                                through the leaderboard?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">We occasionally host tournaments and
                                events linked to the leaderboard. Stay tuned to our announcements and newsletters for
                                updates on upcoming tournaments.</dd>
                        </div>
                        <div>
                            <dt class="text-base font-semibold leading-7 text-white">How can I improve my ranking on
                                the leaderboard?
                            </dt>
                            <dd class="mt-2 text-base leading-7 text-gray-300">To improve your ranking, focus on
                                strategic gameplay, participate in more matches, and aim for higher scores. Engaging
                                with the community and learning from top players can also help you enhance your skills.
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

    </div>
