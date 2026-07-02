@extends('layouts.app')
@section('title', $title)
@section('content')
<x-page-header title="{{ $title }}" subtitle="{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}">
    <x-btn href="{{ route('reports.index') }}" variant="ghost">← All Reports</x-btn>
</x-page-header>

<x-card class="mb-5" padding="p-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div><label class="block text-xs text-slate-500 mb-1">From</label>
            <input name="from" type="date" value="{{ $from->toDateString() }}" class="px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 outline-none"></div>
        <div><label class="block text-xs text-slate-500 mb-1">To</label>
            <input name="to" type="date" value="{{ $to->toDateString() }}" class="px-3 py-2 text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 outline-none"></div>
        <x-btn type="submit" variant="secondary">Apply</x-btn>
        <div class="ml-auto flex gap-2">
            <x-btn href="{{ route('reports.export', [$type,'pdf']) }}?from={{ $from->toDateString() }}&to={{ $to->toDateString() }}" variant="secondary" data-spa-ignore>PDF</x-btn>
            <x-btn href="{{ route('reports.export', [$type,'excel']) }}?from={{ $from->toDateString() }}&to={{ $to->toDateString() }}" variant="secondary" data-spa-ignore>Excel</x-btn>
            <x-btn href="{{ route('reports.export', [$type,'csv']) }}?from={{ $from->toDateString() }}&to={{ $to->toDateString() }}" variant="secondary" data-spa-ignore>CSV</x-btn>
        </div>
    </form>
</x-card>

@if (!empty($report['summary']))
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        @foreach ($report['summary'] as $label => $value)
            <x-card><p class="text-sm text-slate-500">{{ $label }}</p><p class="text-2xl font-extrabold mt-1">{{ $value }}</p></x-card>
        @endforeach
    </div>
@endif

<x-card padding="p-0">
    @if (count($report['rows']))
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-400 bg-slate-50 dark:bg-slate-800/50">
                <tr>@foreach ($report['headers'] as $h)<th class="px-5 py-3 font-medium whitespace-nowrap">{{ $h }}</th>@endforeach</tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($report['rows'] as $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        @foreach ($row as $cell)<td class="px-5 py-2.5 whitespace-nowrap">{{ $cell }}</td>@endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else <x-empty message="No data for this period." /> @endif
</x-card>
@endsection
