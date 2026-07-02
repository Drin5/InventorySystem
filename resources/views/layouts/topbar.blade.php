<header class="sticky top-0 z-20 h-14 glass border-b border-slate-200 dark:border-ink-800 flex items-center gap-3 px-4 sm:px-6">
    <button @click="sidebar = !sidebar" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-slate-100 dark:hover:bg-ink-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors duration-150">
        <i data-lucide="panel-left" class="w-[18px] h-[18px]"></i>
    </button>

    <h1 data-spa-page-title class="text-[15px] font-600 text-slate-900 dark:text-white hidden sm:block">@yield('title', 'Dashboard')</h1>

    <!-- Scan / search -->
    <form method="GET" action="{{ route('variants.lookup') }}" class="ml-auto hidden md:block relative">
        <i data-lucide="search" class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input name="code" placeholder="Scan barcode or search SKU…" autocomplete="off"
               class="w-72 pl-8 pr-3 py-1.5 text-[13px] rounded-md bg-slate-100 dark:bg-ink-800 border border-transparent focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25 focus:bg-white dark:focus:bg-ink-900 outline-none transition-colors duration-150">
    </form>

    <!-- Dark mode -->
    <button @click="dark = !dark" class="grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-slate-100 dark:hover:bg-ink-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors duration-150 ml-auto md:ml-0" title="Toggle theme">
        <i x-show="!dark" data-lucide="moon" class="w-[18px] h-[18px]"></i>
        <i x-show="dark" x-cloak data-lucide="sun" class="w-[18px] h-[18px]"></i>
    </button>

    <!-- Notifications -->
    <div x-data="{ open:false }" class="relative">
        <button @click="open=!open" class="relative grid place-items-center w-8 h-8 rounded-md text-slate-500 hover:bg-slate-100 dark:hover:bg-ink-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors duration-150">
            <i data-lucide="bell" class="w-[18px] h-[18px]"></i>
            @if ($navUnread > 0)
                <span class="absolute top-0.5 right-0.5 min-w-[16px] h-4 px-1 rounded-full bg-rose-500 text-white text-[10px] grid place-items-center font-600 tnum">{{ $navUnread }}</span>
            @endif
        </button>
        <div x-show="open" x-cloak @click.outside="open=false" x-transition.opacity.duration.150ms
             class="absolute right-0 mt-2 w-80 bg-white dark:bg-ink-900 rounded-lg shadow-pop border border-slate-200 dark:border-ink-800 overflow-hidden">
            <div class="px-3.5 py-2.5 border-b border-slate-200 dark:border-ink-800 flex items-center justify-between">
                <span class="text-[13px] font-600 text-slate-900 dark:text-white">Notifications</span>
                <a href="{{ route('notifications.index') }}" data-spa-prefetch class="text-xs text-brand-600 hover:text-brand-700">View all</a>
            </div>
            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-ink-800">
                @forelse ($navNotifications as $n)
                    <div class="px-3.5 py-2.5 flex gap-2.5 hover:bg-slate-50 dark:hover:bg-ink-800/60 transition-colors duration-150">
                        <span class="mt-1.5 w-1.5 h-1.5 rounded-full shrink-0 {{ ['danger'=>'bg-rose-500','warning'=>'bg-amber-500','success'=>'bg-emerald-500','info'=>'bg-brand-500'][$n->level] ?? 'bg-slate-400' }}"></span>
                        <div class="min-w-0">
                            <p class="text-[13px] font-500 text-slate-800 dark:text-slate-100">{{ $n->title }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $n->message }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="px-4 py-6 text-center text-[13px] text-slate-400">You're all caught up.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- User menu -->
    <div x-data="{ open:false }" class="relative">
        <button @click="open=!open" class="flex items-center gap-2 pl-1 pr-1.5 py-1 rounded-md hover:bg-slate-100 dark:hover:bg-ink-800 transition-colors duration-150">
            <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-md object-cover border border-slate-200 dark:border-ink-700" alt="">
            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
        </button>
        <div x-show="open" x-cloak @click.outside="open=false" x-transition.opacity.duration.150ms
             class="absolute right-0 mt-2 w-52 bg-white dark:bg-ink-900 rounded-lg shadow-pop border border-slate-200 dark:border-ink-800 overflow-hidden py-1">
            <div class="px-3.5 py-2.5 border-b border-slate-200 dark:border-ink-800">
                <p class="text-[13px] font-600 truncate text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" data-spa-prefetch class="flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-ink-800 transition-colors duration-150"><i data-lucide="user" class="w-4 h-4 text-slate-400"></i> My Profile</a>
            @can('manage settings')<a href="{{ route('settings.edit') }}" data-spa-prefetch class="flex items-center gap-2.5 px-3.5 py-2 text-[13px] text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-ink-800 transition-colors duration-150"><i data-lucide="settings" class="w-4 h-4 text-slate-400"></i> Settings</a>@endcan
            <div class="my-1 border-t border-slate-100 dark:border-ink-800"></div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="w-full flex items-center gap-2.5 text-left px-3.5 py-2 text-[13px] text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors duration-150"><i data-lucide="log-out" class="w-4 h-4"></i> Log out</button>
            </form>
        </div>
    </div>
</header>
