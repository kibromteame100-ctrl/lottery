@extends('layouts.admin')
@section('title', $lottery->name)

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <a href="{{ route('admin.lotteries.index') }}"
           class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
            ← {{ __('admin.back') }}
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $lottery->name }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $lottery->description }}</p>
        </div>
        <div class="flex gap-2 shrink-0">
            @can('update', $lottery)
            <a href="{{ route('admin.lotteries.edit', $lottery) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold
                      px-4 py-2 rounded-lg transition-colors">
                {{ __('admin.edit') }}
            </a>
            @endcan
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $statCards = [
            ['label' => 'Total Tickets',   'value' => number_format($lottery->ticket_purchases_count), 'color' => 'indigo'],
            ['label' => 'Pending',          'value' => number_format($lottery->pending_count),          'color' => 'yellow'],
            ['label' => 'Approved',         'value' => number_format($lottery->approved_count),         'color' => 'green'],
            ['label' => 'Rejected',         'value' => number_format($lottery->rejected_count),         'color' => 'red'],
        ];
        $colorMap = [
            'indigo' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-700',
            'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700',
            'green'  => 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border-green-200 dark:border-green-700',
            'red'    => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-700',
        ];
        @endphp
        @foreach($statCards as $card)
        <div class="rounded-xl border p-4 {{ $colorMap[$card['color']] }}">
            <div class="text-2xl font-bold">{{ $card['value'] }}</div>
            <div class="text-xs font-medium opacity-80 mt-1">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Details --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-700 dark:text-gray-300 mb-4 pb-2 border-b dark:border-gray-700">
            Lottery Details
        </h2>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Ticket Price</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                    {{ number_format($lottery->ticket_price, 2) }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Draw Date</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                    {{ $lottery->draw_date->format('M d, Y H:i') }}
                </dd>
                <dd class="text-xs text-gray-400">{{ $lottery->draw_date->diffForHumans() }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Number Format</dt>
                <dd class="font-mono font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                    {{ $lottery->number_prefix }}-YYYY-{{ str_repeat('X', $lottery->number_length) }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Max Tickets</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                    {{ $lottery->max_tickets ? number_format($lottery->max_tickets) : 'Unlimited' }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="mt-0.5">
                    @include('components.status-badge', ['status' => $lottery->status])
                </dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Total Revenue</dt>
                <dd class="font-bold text-green-600 dark:text-green-400 mt-0.5">
                    {{ number_format($lottery->totalRevenue(), 2) }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Recent tickets --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">Recent Tickets</h2>
            <a href="{{ route('admin.tickets.index', ['lottery_id' => $lottery->id]) }}"
               class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th class="px-4 py-3 font-medium">Customer</th>
                        <th class="px-4 py-3 font-medium">Transaction ID</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentTickets as $ticket)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $ticket->user?->display_name ?? '—' }}
                            </a>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-300">
                            {{ $ticket->transaction_id }}
                        </td>
                        <td class="px-4 py-3">
                            @include('components.status-badge', ['status' => $ticket->status])
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $ticket->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                            {{ __('admin.no_records') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
