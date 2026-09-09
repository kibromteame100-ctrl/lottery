@extends('layouts.admin')
@section('title', __('admin.tickets'))

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.tickets') }}</h1>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.tickets.index') }}"
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('admin.search') }} (name, phone, transaction ID)"
                   class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                          text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2
                          focus:ring-indigo-500">

            <select name="status"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2
                           focus:ring-indigo-500">
                <option value="">{{ __('admin.all_statuses') }}</option>
                @foreach(['pending','approved','rejected','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>

            <select name="lottery_id"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2
                           focus:ring-indigo-500">
                <option value="">{{ __('admin.all_lotteries') }}</option>
                @foreach($lotteries as $lottery)
                <option value="{{ $lottery->id }}" @selected(request('lottery_id') == $lottery->id)>
                    {{ $lottery->name }}
                </option>
                @endforeach
            </select>

            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                {{ __('admin.filter') }}
            </button>
            @if(request()->hasAny(['search','status','lottery_id']))
            <a href="{{ route('admin.tickets.index') }}"
               class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 px-3 py-2">
                {{ __('admin.clear') }}
            </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th class="px-4 py-3 font-medium">{{ __('admin.id') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.name') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.lottery') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.transaction_id') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.quantity') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.unit_price') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.total') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.date') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">#{{ $ticket->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                {{ $ticket->user?->display_name ?? '—' }}
                            </div>
                            <div class="text-xs text-gray-400">{{ $ticket->user?->phone ?? $ticket->user?->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $ticket->lottery?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600 dark:text-gray-300">
                            {{ $ticket->transaction_id }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(($ticket->quantity ?? 1) > 1)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-xs font-bold">
                                    {{ $ticket->quantity }}
                                </span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">1</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                            ETB {{ number_format($ticket->ticket_price, 2) }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">
                            ETB {{ number_format($ticket->total_price ?? $ticket->ticket_price, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            @include('components.status-badge', ['status' => $ticket->status])
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap">
                            {{ $ticket->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium">
                                {{ __('admin.view') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-10 text-center text-gray-400">
                            {{ __('admin.no_records') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="px-4 py-3 border-t dark:border-gray-700">
            {{ $tickets->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
