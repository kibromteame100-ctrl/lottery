@extends('layouts.admin')
@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="space-y-6 max-w-4xl">

    {{-- Back link --}}
    <a href="{{ route('admin.tickets.index') }}"
       class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
        ← {{ __('admin.back') }}
    </a>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Ticket #{{ $ticket->id }}
        </h1>
        @include('components.status-badge', ['status' => $ticket->status])
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- ── Ticket Details ──────────────────────────────────────────── --}}
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <h2 class="font-semibold text-gray-700 dark:text-gray-300 mb-4 pb-2
                            border-b dark:border-gray-700">Payment Details</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Customer</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            <a href="{{ route('admin.users.show', $ticket->user) }}"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $ticket->user?->display_name }}
                            </a>
                        </dd>
                        <dd class="text-xs text-gray-400">
                            {{ $ticket->user?->phone ?? $ticket->user?->email }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Lottery</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ $ticket->lottery?->name }}
                        </dd>
                        <dd class="text-xs text-gray-400">Draw: {{ $ticket->lottery?->draw_date?->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Transaction ID</dt>
                        <dd class="font-mono font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ $ticket->transaction_id }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Payment Method</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ $ticket->payment_method }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Amount</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            ETB {{ number_format($ticket->ticket_price, 2) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Quantity</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ $ticket->quantity ?? 1 }} ticket(s)
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">Total Paid</dt>
                        <dd class="mt-1">
                            <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                ETB {{ number_format($ticket->total_price ?? $ticket->ticket_price, 2) }}
                            </span>
                            @if(($ticket->quantity ?? 1) > 1)
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                ({{ $ticket->quantity }} × ETB {{ number_format($ticket->ticket_price, 2) }})
                            </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Submitted</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ $ticket->created_at->format('M d, Y H:i') }}
                        </dd>
                    </div>

                    @if($ticket->reviewer)
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Reviewed By</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                            {{ $ticket->reviewer->name }}
                        </dd>
                        <dd class="text-xs text-gray-400">{{ $ticket->reviewed_at?->format('M d, Y H:i') }}</dd>
                    </div>
                    @endif

                    @if($ticket->isApproved() && $ticket->lotteryNumber)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">Lottery Number</dt>
                        <dd class="mt-1">
                            <span class="inline-block bg-green-100 dark:bg-green-900/40 text-green-800
                                         dark:text-green-300 font-mono font-bold text-lg px-4 py-2 rounded-lg">
                                {{ $ticket->lotteryNumber->number }}
                            </span>
                        </dd>
                    </div>
                    @endif

                    @if($ticket->isRejected() && $ticket->rejection_reason)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 dark:text-gray-400">Rejection Reason</dt>
                        <dd class="mt-1 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700
                                   text-red-700 dark:text-red-300 rounded-lg px-4 py-3 text-sm">
                            {{ $ticket->rejection_reason }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- ── Approve / Reject actions ──────────────────────────── --}}
            @if($ticket->isPending())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 space-y-4">
                <h2 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Review Actions</h2>

                {{-- Approve --}}
                @can('approve', $ticket)
                <form method="POST" action="{{ route('admin.tickets.approve', $ticket) }}"
                      onsubmit="return confirm('Approve this ticket and generate a lottery number?')">
                    @csrf
                    <button type="submit"
                            class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold
                                   px-6 py-2.5 rounded-lg transition-colors">
                        ✅ {{ __('admin.approve') }}
                    </button>
                </form>
                @endcan

                {{-- Reject --}}
                @can('reject', $ticket)
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                            class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-semibold
                                   px-6 py-2.5 rounded-lg transition-colors">
                        ❌ {{ __('admin.reject') }}
                    </button>
                    <div x-show="open" x-cloak class="mt-3">
                        <form method="POST" action="{{ route('admin.tickets.reject', $ticket) }}">
                            @csrf
                            <div class="space-y-3">
                                <textarea name="reason" rows="3" required
                                          placeholder="Enter rejection reason (required)..."
                                          class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                                 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                                 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="text-red-500 text-xs">{{ $message }}</p>
                                @enderror
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg text-sm transition-colors">
                                    Confirm Rejection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
            @endif
        </div>

        {{-- ── Screenshot ───────────────────────────────────────────────── --}}
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <h2 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">Payment Screenshot</h2>
                @if($ticket->screenshot_path)
                <a href="{{ route('admin.tickets.screenshot', $ticket) }}" target="_blank">
                    <img src="{{ route('admin.tickets.screenshot', $ticket) }}"
                         alt="Payment Screenshot"
                         class="w-full rounded-lg border dark:border-gray-700 hover:opacity-90 transition-opacity
                                max-h-96 object-contain bg-gray-50 dark:bg-gray-900"
                         loading="lazy">
                </a>
                <a href="{{ route('admin.tickets.screenshot', $ticket) }}" target="_blank"
                   class="mt-2 inline-block text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                    Open full size ↗
                </a>
                @else
                <div class="text-center py-8 text-gray-400">
                    <div class="text-4xl mb-2">📷</div>
                    <p class="text-sm">No screenshot uploaded</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
