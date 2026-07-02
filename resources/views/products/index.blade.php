@extends('layouts.app')
@section('title', 'Products')

@section('content')
<x-page-header title="Products" subtitle="{{ $products->total() }} items in catalog">
    <x-btn href="{{ route('products.create') }}"><i data-lucide="plus"></i> Add Item</x-btn>
</x-page-header>

<!-- Filters -->
<form method="GET" class="flex flex-wrap items-center gap-2 mb-3">
    <div class="relative flex-1 min-w-[220px]">
        <i data-lucide="search" class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <input name="search" value="{{ request('search') }}" placeholder="Search name, SKU or barcode…"
               class="w-full pl-8 pr-3 py-2 text-[13px] rounded-md bg-white dark:bg-ink-800 border border-slate-300 dark:border-ink-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25 transition-colors duration-150">
    </div>
    <select name="category" class="px-3 py-2 text-[13px] rounded-md bg-white dark:bg-ink-800 border border-slate-300 dark:border-ink-700 outline-none focus:border-brand-500">
        <option value="">All categories</option>
        @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="brand" class="px-3 py-2 text-[13px] rounded-md bg-white dark:bg-ink-800 border border-slate-300 dark:border-ink-700 outline-none focus:border-brand-500">
        <option value="">All brands</option>
        @foreach ($brands as $b)<option value="{{ $b->id }}" @selected(request('brand')==$b->id)>{{ $b->name }}</option>@endforeach
    </select>
    <select name="status" class="px-3 py-2 text-[13px] rounded-md bg-white dark:bg-ink-800 border border-slate-300 dark:border-ink-700 outline-none focus:border-brand-500">
        <option value="">Any status</option>
        <option value="active" @selected(request('status')=='active')>Active</option>
        <option value="inactive" @selected(request('status')=='inactive')>Inactive</option>
    </select>
    <x-btn type="submit" variant="secondary"><i data-lucide="filter"></i> Filter</x-btn>
    @if(request()->hasAny(['search','category','brand','status']))
        <a href="{{ route('products.index') }}" class="text-[13px] text-slate-500 hover:text-slate-700 px-2">Clear</a>
    @endif
</form>

@if ($products->count())
    <div class="bg-white dark:bg-ink-900 rounded-lg border border-slate-200 dark:border-ink-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-[13px]">
                <colgroup>
                    <col class="w-[36%]">
                    <col class="w-[16%]">
                    <col class="w-[18%]">
                    <col class="w-[12%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                    <col class="w-[8%]">
                </colgroup>
                <thead class="z-10">
                    <tr class="text-left text-[11px] uppercase tracking-wide text-slate-400 bg-slate-50 dark:bg-ink-950/60 border-b border-slate-200 dark:border-ink-800">
                        <th class="pl-14 pr-4 py-2.5 font-600">Item</th>
                        <th class="px-4 py-2.5 font-600 whitespace-nowrap">SKU</th>
                        <th class="px-4 py-2.5 font-600 whitespace-nowrap">Category</th>
                        <th class="px-4 py-2.5 font-600 text-right whitespace-nowrap">Price</th>
                        <th class="px-4 py-2.5 font-600 text-right whitespace-nowrap">On Hand</th>
                        <th class="px-4 py-2.5 font-600 whitespace-nowrap">Status</th>
                        <th class="px-4 py-2.5 font-600 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-ink-800">
                    @foreach ($products as $product)
                        @php $stock = $product->total_stock; @endphp
                        <tr class="group hover:bg-slate-50 dark:hover:bg-ink-800/40 transition-colors duration-150">
                            <td class="px-4 py-2.5">
                                <a href="{{ route('products.show', $product) }}" class="flex items-center gap-3 min-w-0">
                                    <img src="{{ $product->image_url }}" alt="" class="w-8 h-8 rounded-md object-cover border border-slate-200 dark:border-ink-700 shrink-0">
                                    <span class="font-500 text-slate-800 dark:text-slate-100 group-hover:text-brand-600 truncate">{{ $product->name }}</span>
                                </a>
                            </td>
                            <td class="px-4 py-2.5 tnum text-slate-500 whitespace-nowrap">{{ $product->sku }}</td>
                            <td class="px-4 py-2.5 text-slate-600 dark:text-slate-300 truncate">{{ $product->category?->name ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-right tnum font-500 text-slate-800 dark:text-slate-100 whitespace-nowrap">{{ money($product->selling_price) }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <span class="tnum font-600 {{ $stock<=0 ? 'text-rose-600 dark:text-rose-400' : ($stock<=10 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100') }}">{{ number_format($stock) }}</span>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @if ($stock<=0)
                                    <x-badge color="red" dot>Out of stock</x-badge>
                                @elseif ($stock<=10)
                                    <x-badge color="amber" dot>Low stock</x-badge>
                                @elseif ($product->status==='active')
                                    <x-badge color="green" dot>In stock</x-badge>
                                @else
                                    <x-badge>Inactive</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center justify-end gap-0.5 opacity-60 group-hover:opacity-100 transition-opacity duration-150">
                                    <a href="{{ route('products.edit', $product) }}" title="Edit" class="grid place-items-center w-7 h-7 rounded-md text-slate-400 hover:text-brand-600 hover:bg-slate-100 dark:hover:bg-ink-800 transition-colors duration-150"><i data-lucide="pencil" class="w-[15px] h-[15px]"></i></a>
                                    <a href="{{ route('products.labels', $product) }}" title="Print labels" class="grid place-items-center w-7 h-7 rounded-md text-slate-400 hover:text-brand-600 hover:bg-slate-100 dark:hover:bg-ink-800 transition-colors duration-150"><i data-lucide="tag" class="w-[15px] h-[15px]"></i></a>
                                    <x-delete :action="route('products.destroy', $product)" title="Delete item?" message="“{{ $product->name }}” will be permanently removed from your catalog. This cannot be undone." />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
@else
    <div class="bg-white dark:bg-ink-900 rounded-lg border border-slate-200 dark:border-ink-800">
        <x-empty message="No products match your filters" icon="package-search" hint="Try adjusting your search or filters, or add a new item.">
            <x-btn href="{{ route('products.create') }}"><i data-lucide="plus"></i> Add Item</x-btn>
        </x-empty>
    </div>
@endif
@endsection
