@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@if (($dashboardMode ?? 'default') === 'cashier')
    @php
        $greeting = now()->hour < 12 ? 'Good morning' : (now()->hour < 18 ? 'Good afternoon' : 'Good evening');
        $paymentLabels = [
            'cash' => 'Cash',
            'card' => 'Card',
            'gcash' => 'GCash',
            'bank' => 'Bank',
        ];
    @endphp

    <section class="relative overflow-hidden rounded-[28px] border border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(63,96,138,.18),_transparent_42%),linear-gradient(135deg,_#ffffff_0%,_#f8fafc_55%,_#eef2f8_100%)] px-6 py-6 shadow-card dark:border-ink-800 dark:bg-[radial-gradient(circle_at_top_left,_rgba(95,128,168,.16),_transparent_38%),linear-gradient(135deg,_#0f172a_0%,_#111c2f_55%,_#1a2940_100%)] sm:px-8 sm:py-8">
        <div class="absolute -right-10 top-0 h-36 w-36 rounded-full bg-brand-300/25 blur-3xl dark:bg-brand-500/20"></div>
        <div class="absolute bottom-0 left-1/3 h-28 w-28 rounded-full bg-emerald-300/20 blur-3xl dark:bg-emerald-500/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-[11px] font-600 uppercase tracking-[0.22em] text-brand-700/80 dark:text-brand-300/80">Cashier Station</p>
                <h1 class="mt-2 text-3xl font-700 tracking-tight text-slate-900 dark:text-white sm:text-4xl">{{ $greeting }}, {{ auth()->user()->name }}</h1>
                <p class="mt-3 max-w-xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                    This screen stays focused on checkout so the counter always looks calm, clear, and ready for the next customer.
                </p>
                <div class="mt-5 flex flex-wrap items-center gap-2.5 text-xs text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1.5 shadow-sm dark:bg-white/10">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Ready to ring up sales
                    </span>
                    <span>{{ now()->format('l, M j, Y') }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-btn href="{{ route('pos') }}" class="!rounded-xl !px-5 !py-3 !text-sm shadow-lg shadow-brand-600/20">
                    <i data-lucide="scan-line"></i>
                    Open Checkout
                </x-btn>
                @can('manage customers')
                    <x-btn href="{{ route('customers.index') }}" variant="secondary" class="!rounded-xl !px-5 !py-3 !text-sm bg-white/75 dark:bg-white/10">
                        <i data-lucide="users"></i>
                        Customers
                    </x-btn>
                @endcan
            </div>
        </div>
    </section>

    <div class="mt-5 grid grid-cols-2 gap-3 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card dark:border-ink-800 dark:bg-ink-900">
            <p class="text-[11px] font-600 uppercase tracking-wide text-slate-400">Sales Today</p>
            <p class="mt-2 text-3xl font-700 tracking-tight text-slate-900 dark:text-white">{{ money($cashierMetrics['today_sales']) }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Total value processed at this station today</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card dark:border-ink-800 dark:bg-ink-900">
            <p class="text-[11px] font-600 uppercase tracking-wide text-slate-400">Transactions</p>
            <p class="mt-2 text-3xl font-700 tracking-tight text-slate-900 dark:text-white">{{ number_format($cashierMetrics['today_orders']) }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Completed receipts under your login</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card dark:border-ink-800 dark:bg-ink-900">
            <p class="text-[11px] font-600 uppercase tracking-wide text-slate-400">Items Rung Up</p>
            <p class="mt-2 text-3xl font-700 tracking-tight text-slate-900 dark:text-white">{{ number_format($cashierMetrics['items_rung']) }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pieces sold across today's transactions</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-card dark:border-ink-800 dark:bg-ink-900">
            <p class="text-[11px] font-600 uppercase tracking-wide text-slate-400">Average Sale</p>
            <p class="mt-2 text-3xl font-700 tracking-tight text-slate-900 dark:text-white">{{ money($cashierMetrics['average_sale']) }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Average basket value for this cashier today</p>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-4 xl:grid-cols-[1.3fr_.7fr]">
        <x-card padding="p-0" class="overflow-hidden rounded-[24px]">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-ink-800">
                <div>
                    <h3 class="text-[14px] font-600 text-slate-900 dark:text-white">Recent Receipts</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Only the cashier-facing sales activity is shown here.</p>
                </div>
                <a href="{{ route('sales.index') }}" class="text-xs font-600 text-brand-600 hover:text-brand-700">View history</a>
            </div>

            @if ($recentSales->isNotEmpty())
                <div class="divide-y divide-slate-100 dark:divide-ink-800">
                    @foreach ($recentSales as $sale)
                        <a href="{{ route('sales.show', $sale) }}" class="flex items-center gap-4 px-5 py-4 transition-colors duration-150 hover:bg-slate-50 dark:hover:bg-ink-800/50">
                            <div class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-900/20 dark:text-brand-300">
                                <i data-lucide="receipt-text" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                    <p class="font-600 text-slate-900 dark:text-white">{{ $sale->invoice_number }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $sale->created_at->format('g:i A') }}</p>
                                </div>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $sale->items_count }} {{ \Illuminate\Support\Str::plural('item', $sale->items_count) }}
                                    @if ($sale->customer)
                                        • {{ $sale->customer->name }}
                                    @else
                                        • Walk-in customer
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-600 text-slate-900 dark:text-white">{{ money($sale->total) }}</p>
                                <p class="mt-1 text-xs uppercase tracking-wide text-slate-400">{{ $paymentLabels[$sale->payment_method] ?? ucfirst($sale->payment_method) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-12 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-ink-800 dark:text-slate-500">
                        <i data-lucide="receipt-text" class="w-6 h-6"></i>
                    </div>
                    <h4 class="mt-4 text-sm font-600 text-slate-900 dark:text-white">No receipts yet today</h4>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Start the next transaction from the checkout button above.</p>
                </div>
            @endif
        </x-card>

        <div class="space-y-4">
            <x-card padding="p-5" class="rounded-[24px]">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-[14px] font-600 text-slate-900 dark:text-white">Tender Mix</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Today's completed sales by payment method</p>
                    </div>
                    <div class="grid h-10 w-10 place-items-center rounded-2xl bg-slate-100 text-slate-500 dark:bg-ink-800 dark:text-slate-400">
                        <i data-lucide="wallet-cards" class="w-5 h-5"></i>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($paymentBreakdown as $method => $breakdown)
                        <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-ink-800">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-600 text-slate-900 dark:text-white">{{ $paymentLabels[$method] ?? ucfirst($method) }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ number_format($breakdown['count']) }} {{ \Illuminate\Support\Str::plural('sale', $breakdown['count']) }}</p>
                                </div>
                                <p class="font-600 text-slate-900 dark:text-white">{{ money($breakdown['total']) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-8 text-center dark:border-ink-800">
                            <p class="text-sm text-slate-500 dark:text-slate-400">Payment breakdown will appear after the first completed sale.</p>
                        </div>
                    @endforelse
                </div>
            </x-card>

            <x-card padding="p-5" class="rounded-[24px]">
                <h3 class="text-[14px] font-600 text-slate-900 dark:text-white">Counter Notes</h3>
                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 dark:text-slate-300">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-ink-800/70">
                        Keep this dashboard customer-safe: checkout first, no inventory or admin clutter.
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-ink-800/70">
                        If you need product or stock management, that belongs to staff screens, not the counter.
                    </div>
                </div>
            </x-card>
        </div>
    </div>
@else
    @php
        $tiles = [
            ['label' => 'Total Products', 'value' => number_format($stats['total_products']), 'icon' => 'package', 'tone' => 'neutral', 'href' => route('products.index')],
            ['label' => 'Categories', 'value' => number_format($stats['total_categories']), 'icon' => 'tags', 'tone' => 'neutral', 'href' => route('categories.index')],
            ['label' => 'Low Stock', 'value' => number_format($stats['low_stock']), 'icon' => 'alert-triangle', 'tone' => 'amber', 'href' => route('reports.show', 'low-stock')],
            ['label' => 'Out of Stock', 'value' => number_format($stats['out_of_stock']), 'icon' => 'x-circle', 'tone' => 'red', 'href' => route('reports.show', 'low-stock')],
        ];
        $toneText = ['neutral' => 'text-slate-900 dark:text-white', 'amber' => 'text-amber-600 dark:text-amber-400', 'red' => 'text-rose-600 dark:text-rose-400'];
        $toneIcon = ['neutral' => 'text-slate-400', 'amber' => 'text-amber-500', 'red' => 'text-rose-500'];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-lg font-600 tracking-tight text-slate-900 dark:text-white">Overview</h1>
            <p class="text-[13px] text-slate-500 dark:text-slate-400 mt-0.5">{{ now()->format('l, M j, Y') }} · {{ $stats['today_orders'] }} sales logged today</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('process sales')<x-btn href="{{ route('pos') }}"><i data-lucide="plus"></i> New Sale</x-btn>@endcan
            @can('manage stock_in')<x-btn href="{{ route('stock-ins.create') }}" variant="secondary"><i data-lucide="arrow-down-to-line"></i> Stock In</x-btn>@endcan
            @can('manage products')<x-btn href="{{ route('products.create') }}" variant="secondary"><i data-lucide="plus"></i> Add Item</x-btn>@endcan
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
        @foreach ($tiles as $c)
            <a href="{{ $c['href'] }}" class="group bg-white dark:bg-ink-900 rounded-lg border border-slate-200 dark:border-ink-800 p-4 hover:border-slate-300 dark:hover:border-ink-700 transition-colors duration-150">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-600 uppercase tracking-wide text-slate-400">{{ $c['label'] }}</span>
                    <i data-lucide="{{ $c['icon'] }}" class="w-4 h-4 {{ $toneIcon[$c['tone']] }}"></i>
                </div>
                <p class="tnum text-[28px] leading-none font-600 mt-3 {{ $toneText[$c['tone']] }}">{{ $c['value'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
        <div class="bg-white dark:bg-ink-900 rounded-lg border border-slate-200 dark:border-ink-800 px-4 py-3">
            <p class="text-[11px] font-600 uppercase tracking-wide text-slate-400">Today's Sales</p>
            <p class="tnum text-xl font-600 text-slate-900 dark:text-white mt-1">{{ money($stats['today_sales']) }}</p>
        </div>
        <div class="bg-white dark:bg-ink-900 rounded-lg border border-slate-200 dark:border-ink-800 px-4 py-3">
            <p class="text-[11px] font-600 uppercase tracking-wide text-slate-400">This Month · {{ now()->format('M Y') }}</p>
            <p class="tnum text-xl font-600 text-slate-900 dark:text-white mt-1">{{ money($stats['month_sales']) }}</p>
        </div>
        <div class="bg-white dark:bg-ink-900 rounded-lg border border-slate-200 dark:border-ink-800 px-4 py-3">
            <p class="text-[11px] font-600 uppercase tracking-wide text-slate-400">Inventory Value · at cost</p>
            <p class="tnum text-xl font-600 text-slate-900 dark:text-white mt-1">{{ money($stats['inventory_value']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        <x-card class="lg:col-span-2" padding="p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[13px] font-600 text-slate-900 dark:text-white">Sales - last 14 days</h3>
                <span class="text-[11px] text-slate-400">Revenue</span>
            </div>
            <canvas id="salesChart" height="104"></canvas>
        </x-card>

        <x-card padding="p-4">
            <h3 class="text-[13px] font-600 text-slate-900 dark:text-white mb-3">Best Sellers</h3>
            <div class="-mx-1">
                @forelse ($bestSellers as $i => $b)
                    <div class="flex items-center gap-2.5 px-1 py-1.5 rounded hover:bg-slate-50 dark:hover:bg-ink-800/60 transition-colors duration-150">
                        <span class="tnum w-5 text-center text-xs font-600 text-slate-400">{{ $i + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-500 truncate text-slate-800 dark:text-slate-100">{{ $b->variant?->display_name ?? 'Deleted product' }}</p>
                            <p class="text-[11px] text-slate-400 tnum">{{ $b->sold }} sold</p>
                        </div>
                        <span class="tnum text-[13px] font-500 text-slate-600 dark:text-slate-300">{{ money($b->revenue) }}</span>
                    </div>
                @empty
                    <p class="text-[13px] text-slate-400 py-6 text-center">No sales recorded yet.</p>
                @endforelse
            </div>
        </x-card>

        <x-card class="lg:col-span-2" padding="p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[13px] font-600 text-slate-900 dark:text-white">Stock Movement - last 14 days</h3>
                <div class="flex items-center gap-3 text-[11px] text-slate-500">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-sm bg-emerald-500"></span>In</span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-sm bg-rose-500"></span>Out</span>
                </div>
            </div>
            <canvas id="movementChart" height="104"></canvas>
        </x-card>

        <x-card padding="p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-[13px] font-600 text-slate-900 dark:text-white">Low Stock Alerts</h3>
                <a href="{{ route('reports.show', 'low-stock') }}" class="text-[11px] text-brand-600 hover:text-brand-700">Report</a>
            </div>
            <div class="-mx-1 max-h-72 overflow-y-auto">
                @forelse ($lowStockItems as $v)
                    <div class="flex items-center justify-between gap-2 px-1 py-1.5 rounded hover:bg-slate-50 dark:hover:bg-ink-800/60 transition-colors duration-150">
                        <div class="min-w-0">
                            <p class="text-[13px] font-500 truncate text-slate-800 dark:text-slate-100">{{ $v->display_name }}</p>
                            <p class="text-[11px] text-slate-400 tnum">{{ $v->sku }}</p>
                        </div>
                        @if ($v->stock_quantity <= 0)
                            <x-badge color="red" dot>Out</x-badge>
                        @else
                            <x-badge color="amber" dot><span class="tnum">{{ $v->stock_quantity }}</span> left</x-badge>
                        @endif
                    </div>
                @empty
                    <x-empty message="Everything is in stock" icon="check-circle-2" hint="No items below their reorder level." />
                @endforelse
            </div>
        </x-card>
    </div>

    <x-card class="mt-3" padding="p-0">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-ink-800">
            <h3 class="text-[13px] font-600 text-slate-900 dark:text-white">Recent Stock Movements</h3>
            <a href="{{ route('activity-logs.index') }}" class="text-[11px] text-brand-600 hover:text-brand-700">View activity log</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-slate-400 border-b border-slate-200 dark:border-ink-800">
                        <th class="px-4 py-2 font-600">Item</th>
                        <th class="px-4 py-2 font-600">Type</th>
                        <th class="px-4 py-2 font-600 text-right">Change</th>
                        <th class="px-4 py-2 font-600">User</th>
                        <th class="px-4 py-2 font-600 text-right">When</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-ink-800">
                    @forelse ($recentActivities as $m)
                        <tr class="hover:bg-slate-50 dark:hover:bg-ink-800/40 transition-colors duration-150">
                            <td class="px-4 py-2.5 font-500 text-slate-800 dark:text-slate-100">{{ $m->variant?->display_name ?? '-' }}</td>
                            <td class="px-4 py-2.5"><x-badge color="{{ $m->direction === 'in' ? 'green' : 'red' }}">{{ ucfirst(str_replace('_', ' ', $m->type)) }}</x-badge></td>
                            <td class="px-4 py-2.5 text-right tnum font-600 {{ $m->direction === 'in' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">{{ $m->direction === 'in' ? '+' : '-' }}{{ $m->quantity }}</td>
                            <td class="px-4 py-2.5 text-slate-500">{{ $m->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-2.5 text-right text-slate-400 whitespace-nowrap">{{ $m->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-8 text-center text-slate-400">No stock movements yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @push('scripts')
    <script>
    (async () => {
        const res = await fetch('{{ route('dashboard.chart') }}', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        const dark = document.documentElement.classList.contains('dark');
        const grid = dark ? 'rgba(148,163,184,.10)' : 'rgba(100,116,139,.12)';
        const tick = '#94a3b8';
        const font = { family: 'Inter', size: 11 };
        const opts = {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 6, titleFont: { family: 'Inter', weight: '600', size: 12 }, bodyFont: font, bodyColor: '#cbd5e1' } },
            scales: { y: { beginAtZero: true, border: { display: false }, grid: { color: grid }, ticks: { color: tick, font } }, x: { border: { display: false }, grid: { display: false }, ticks: { color: tick, font } } }
        };

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: { labels: data.labels, datasets: [{ label: 'Sales', data: data.sales, borderColor: '#324c6e', backgroundColor: 'rgba(50,76,110,.06)', fill: true, tension: .3, pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: '#324c6e', borderWidth: 2 }] },
            options: opts
        });
        new Chart(document.getElementById('movementChart'), {
            type: 'bar',
            data: { labels: data.labels, datasets: [
                { label: 'Stock In', data: data.stock_in, backgroundColor: '#10b981', borderRadius: 3, maxBarThickness: 12 },
                { label: 'Stock Out', data: data.stock_out, backgroundColor: '#f43f5e', borderRadius: 3, maxBarThickness: 12 }
            ] },
            options: opts
        });
    })();
    </script>
    @endpush
@endif
@endsection
