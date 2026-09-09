@extends('layouts.admin')
@section('title', __('admin.reports'))

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.reports') }}</h1>

    {{-- Date filter --}}
    <form method="GET" action="{{ route('admin.reports.index') }}"
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div class="flex flex-col sm:flex-row gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('admin.from') }}</label>
                <input type="date" name="from" value="{{ $from }}"
                       class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                              text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('admin.to') }}</label>
                <input type="date" name="to" value="{{ $to }}"
                       class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                              text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                {{ __('admin.filter') }}
            </button>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $summaryCards = [
                ['label' => __('admin.total_transactions'), 'value' => number_format($summary->total_transactions ?? $summary->total ?? 0), 'color' => 'indigo'],
                ['label' => __('admin.approved'),  'value' => number_format($summary->approved ?? 0),  'color' => 'green'],
                ['label' => __('admin.rejected'),  'value' => number_format($summary->rejected ?? 0),  'color' => 'red'],
                ['label' => __('admin.pending'),   'value' => number_format($summary->pending ?? 0),   'color' => 'yellow'],
                ['label' => __('admin.revenue'),   'value' => 'ETB ' . number_format($summary->revenue ?? 0, 2), 'color' => 'blue'],
            ];
        @endphp
        @foreach($summaryCards as $card)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $card['value'] }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- By Lottery --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ __('admin.breakdown_by_lottery') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th class="px-4 py-3 font-medium">{{ __('admin.lottery') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.total') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.approved') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.rejected') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.revenue') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($byLottery as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $row->lottery?->name ?? __('admin.unknown') }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ number_format($row->total_tickets ?? $row->total ?? 0) }}</td>
                        <td class="px-4 py-3 text-green-600 dark:text-green-400">{{ number_format($row->approved ?? 0) }}</td>
                        <td class="px-4 py-3 text-red-500 dark:text-red-400">{{ number_format($row->rejected ?? 0) }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">ETB {{ number_format($row->revenue ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">{{ __('admin.no_records') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daily Sales --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ __('admin.daily_sales') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th class="px-4 py-3 font-medium">{{ __('admin.date') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.tickets_count') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.revenue') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($dailySales as $day)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $day->date }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ number_format($day->count) }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">ETB {{ number_format($day->revenue, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-400">{{ __('admin.no_records') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
