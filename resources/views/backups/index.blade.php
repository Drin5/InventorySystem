@extends('layouts.app')
@section('title', 'Backup & Restore')
@section('content')
<x-page-header title="Database Backup" subtitle="Create and download database backups">
    <form method="POST" action="{{ route('backups.create') }}">@csrf
        <x-btn type="submit">＋ Create Backup</x-btn>
    </form>
</x-page-header>
<x-card padding="p-0">
    @if ($files->count())
    <table class="w-full text-sm">
        <thead class="text-left text-slate-400 bg-slate-50 dark:bg-slate-800/50">
            <tr><th class="px-5 py-3 font-medium">File</th><th class="px-5 py-3 font-medium">Size</th><th class="px-5 py-3 font-medium">Created</th><th class="px-5 py-3 font-medium text-right">Actions</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach ($files as $f)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                    <td class="px-5 py-3 font-mono text-xs">{{ $f['name'] }}</td>
                    <td class="px-5 py-3">{{ $f['size'] }} KB</td>
                    <td class="px-5 py-3 text-slate-500">{{ $f['date'] }}</td>
                    <td class="px-5 py-3"><div class="flex items-center justify-end gap-2">
                        <x-btn href="{{ route('backups.download', $f['name']) }}" variant="ghost" class="!px-2.5 !py-1.5 text-xs" data-spa-ignore>Download</x-btn>
                        <form method="POST" action="{{ route('backups.destroy', $f['name']) }}" onsubmit="return confirm('Delete this backup?')">@csrf @method('DELETE')
                            <button class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </form>
                    </div></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else <x-empty message="No backups yet. Create your first backup above." /> @endif
</x-card>
<p class="mt-4 text-xs text-slate-400">Backups are stored in <code>storage/app/backups</code> using mysqldump from your XAMPP installation.</p>
@endsection
