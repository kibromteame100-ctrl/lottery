@extends('layouts.admin')
@section('title', $user->name)

@section('content')
<div class="space-y-6 max-w-4xl">
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
        ← {{ __('admin.back') }}
    </a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-indigo-600 text-white flex items-center justify-center
                             text-2xl font-bold uppercase">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ $user->phone ?? $user->email }}
                    </p>
                    <div class="mt-1">
                        @include('components.status-badge', ['status' => $user->status])
                    </div>
                </div>
            </div>

            @role('super-admin')
            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="text-sm font-semibold px-4 py-2 rounded-lg transition-colors
                               {{ $user->status === 'active'
                                  ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400'
                                  : 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400' }}">
                    {{ $user->status === 'active' ? 'Suspend Account' : 'Activate Account' }}
                </button>
            </form>
            @endrole
        </div>

        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t dark:border-gray-700 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Joined</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                    {{ $user->created_at->format('M d, Y') }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Language</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5 uppercase">
                    {{ $user->preferred_locale }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Total Tickets</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                    {{ $tickets->total() }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Ticket History --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">Ticket History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th class="px-4 py-3 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">Lottery</th>
                        <th class="px-4 py-3 font-medium">Transaction</th>
                        <th class="px-4 py-3 font-medium">Lottery Number</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-4 py-3 text-gray-500">#{{ $ticket->id }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $ticket->lottery?->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $ticket->transaction_id }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-green-600 dark:text-green-400 font-bold">
                            {{ $ticket->lotteryNumber?->number ?? '—' }}
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
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">No tickets found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="px-4 py-3 border-t dark:border-gray-700">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>
@endsection
