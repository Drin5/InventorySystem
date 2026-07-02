@php
    $nav = [
        ['heading' => 'Main'],
        ['label' => 'Dashboard', 'route' => 'dashboard', 'perm' => 'view dashboard', 'lucide' => 'layout-dashboard'],
        ['label' => 'Point of Sale', 'route' => 'pos', 'perm' => 'process sales', 'lucide' => 'scan-line'],
        ['heading' => 'Catalog'],
        ['label' => 'Products', 'route' => 'products.index', 'perm' => 'manage products', 'lucide' => 'package'],
        ['label' => 'Categories', 'route' => 'categories.index', 'perm' => 'manage categories', 'lucide' => 'tags'],
        ['label' => 'Brands', 'route' => 'brands.index', 'perm' => 'manage brands', 'lucide' => 'bookmark'],
        ['label' => 'Units', 'route' => 'units.index', 'perm' => 'manage products', 'lucide' => 'ruler'],
        ['label' => 'Suppliers', 'route' => 'suppliers.index', 'perm' => 'manage suppliers', 'lucide' => 'truck'],
        ['label' => 'Customers', 'route' => 'customers.index', 'perm' => 'manage customers', 'lucide' => 'users'],
        ['heading' => 'Inventory'],
        ['label' => 'Stock In', 'route' => 'stock-ins.index', 'perm' => 'manage stock_in', 'lucide' => 'arrow-down-to-line'],
        ['label' => 'Stock Out', 'route' => 'stock-outs.index', 'perm' => 'manage stock_out', 'lucide' => 'arrow-up-from-line'],
        ['label' => 'Adjustments', 'route' => 'adjustments.index', 'perm' => 'manage adjustments', 'lucide' => 'sliders-horizontal'],
        ['label' => 'Purchase Orders', 'route' => 'purchase-orders.index', 'perm' => 'manage purchase_orders', 'lucide' => 'clipboard-list'],
        ['heading' => 'Sales & Reports'],
        ['label' => 'Sales History', 'route' => 'sales.index', 'perm' => 'process sales', 'lucide' => 'receipt-text'],
        ['label' => 'Reports', 'route' => 'reports.index', 'perm' => 'view reports', 'lucide' => 'bar-chart-3'],
        ['label' => 'Activity Logs', 'route' => 'activity-logs.index', 'perm' => 'view activity_logs', 'lucide' => 'history'],
        ['heading' => 'Administration'],
        ['label' => 'Users & Roles', 'route' => 'users.index', 'perm' => 'manage users', 'lucide' => 'user-cog'],
        ['label' => 'Settings', 'route' => 'settings.edit', 'perm' => 'manage settings', 'lucide' => 'settings'],
        ['label' => 'Backup', 'route' => 'backups.index', 'perm' => 'manage backups', 'lucide' => 'database-backup'],
    ];
@endphp

<!-- Mobile overlay -->
<div x-show="sidebar && window.innerWidth < 1024" x-cloak @click="sidebar=false"
     class="fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

<aside x-show="sidebar" x-cloak
       class="fixed lg:sticky top-0 z-40 h-screen w-60 shrink-0 bg-white dark:bg-ink-900 border-r border-slate-200 dark:border-ink-800 flex flex-col"
       x-transition:enter="transition ease-out duration-150" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
    <div class="h-14 flex items-center gap-2.5 px-4 border-b border-slate-200 dark:border-ink-800">
        <div class="w-8 h-8 rounded-md bg-brand-600 grid place-items-center text-white shrink-0">
            <i data-lucide="boxes" class="w-[18px] h-[18px]"></i>
        </div>
        <div class="leading-tight min-w-0">
            <p class="text-[13px] font-600 text-slate-800 dark:text-white truncate">{{ $shopName }}</p>
            <p class="text-[11px] text-slate-400">Inventory</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-2.5 py-3 space-y-px">
        @foreach ($nav as $item)
            @if (isset($item['heading']))
                <p class="px-2.5 pt-4 pb-1 text-[10px] font-600 uppercase tracking-[.08em] text-slate-400 dark:text-slate-500">{{ $item['heading'] }}</p>
            @elseif (auth()->user()->can($item['perm']))
                @php
                    $active = request()->routeIs(Str::beforeLast($item['route'], '.').'*') || request()->routeIs($item['route']);
                    $lucide = $item['lucide'] ?? 'circle';
                @endphp
                <a href="{{ route($item['route']) }}" data-spa-prefetch
                   class="group relative flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] transition-colors duration-150
                   {{ $active
                        ? 'bg-slate-100 dark:bg-ink-800 text-slate-900 dark:text-white font-500'
                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-ink-800/60 hover:text-slate-900 dark:hover:text-slate-200' }}">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-0.5 rounded-r bg-brand-600 {{ $active ? 'opacity-100' : 'opacity-0' }}"></span>
                    <i data-lucide="{{ $lucide }}" class="w-4 h-4 shrink-0 {{ $active ? 'text-brand-600 dark:text-brand-400' : 'text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300' }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <div class="p-2.5 border-t border-slate-200 dark:border-ink-800">
        <a href="{{ route('profile.edit') }}" data-spa-prefetch class="group flex items-center gap-2.5 p-2 rounded-md hover:bg-slate-50 dark:hover:bg-ink-800/60 transition-colors duration-150">
            <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-md object-cover border border-slate-200 dark:border-ink-700" alt="">
            <div class="min-w-0 flex-1">
                <p class="text-[13px] font-500 truncate text-slate-800 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->role_name }}</p>
            </div>
            <i data-lucide="settings-2" class="w-4 h-4 text-slate-300 group-hover:text-slate-500"></i>
        </a>
    </div>
</aside>
