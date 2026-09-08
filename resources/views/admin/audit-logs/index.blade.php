@extends('layouts.admin')
@section('title', __('admin.audit_logs'))

@section('content')
<div class="space-y-5">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.audit_logs') }}</h1>

    <form method="GET" action="{{ route('admin.audit-logs.index') }}"
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div class="flex flex-wrap gap-3">
            <select name="action"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                          text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <input type="date" name="to" value="{{ request('to') }}"
                   class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                          text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                {{ __('admin.filter') }}
            </button>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th class="px-4 py-3 font-medium">Action</th>
                        <th class="px-4 py-3 font-medium">Admin</th>
                        <th class="px-4 py-3 font-medium">Entity</th>
                        <th class="px-4 py-3 font-medium">IP</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.date') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-4 py-3">
                            <span class="inline-block bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700
                                         dark:text-indigo-300 text-xs font-mono px-2 py-0.5 rounded">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                            {{ $log->admin?->name ?? 'System' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">
                            {{ class_basename($log->entity_type) }} #{{ $log->entity_id ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap">
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.audit-logs.show', $log) }}"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium">
                                {{ __('admin.view') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">{{ __('admin.no_records') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-4 py-3 border-t dark:border-gray-700">{{ $logs->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
