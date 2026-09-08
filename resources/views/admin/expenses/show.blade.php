@extends('layouts.admin')
@section('title', 'Expense #' . $expense->id)

@section('content')
<div class="max-w-3xl space-y-5">

    <a href="{{ route('admin.expenses.index') }}"
       class="text-sm text-[#6c63ff] hover:underline flex items-center gap-1 w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Expenses
    </a>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $expense->title }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Expense #{{ $expense->id }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                          {{ $expense->status_badge_class }}">
                {{ ucfirst($expense->status) }}
            </span>

            @can('update', $expense)
            <a href="{{ route('admin.expenses.edit', $expense) }}"
               class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300
                      font-semibold text-sm px-3 py-1.5 rounded-xl hover:bg-indigo-200 transition-colors">
                Edit
            </a>
            @endcan

            @can('delete', $expense)
            <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}"
                  onsubmit="return confirm('Delete this expense permanently?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300
                               font-semibold text-sm px-3 py-1.5 rounded-xl hover:bg-red-200 transition-colors">
                    Delete
                </button>
            </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Details --}}
        <div class="md:col-span-2 bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-5">
            <h2 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-4 pb-2
                        border-b border-gray-100 dark:border-white/[.05]">Details</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs font-medium mb-0.5">Amount</dt>
                    <dd class="text-2xl font-extrabold text-gray-900 dark:text-white">
                        ETB {{ number_format($expense->amount, 2) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs font-medium mb-0.5">Category</dt>
                    <dd class="font-semibold text-gray-800 dark:text-gray-100">{{ $expense->category_label }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs font-medium mb-0.5">Expense Date</dt>
                    <dd class="font-semibold text-gray-800 dark:text-gray-100">
                        {{ $expense->expense_date->format('M d, Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs font-medium mb-0.5">Submitted By</dt>
                    <dd class="font-semibold text-gray-800 dark:text-gray-100">
                        {{ $expense->creator?->name ?? '—' }}
                    </dd>
                </div>
                @if($expense->approver)
                <div>
                    <dt class="text-gray-400 text-xs font-medium mb-0.5">Reviewed By</dt>
                    <dd class="font-semibold text-gray-800 dark:text-gray-100">{{ $expense->approver->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 text-xs font-medium mb-0.5">Reviewed At</dt>
                    <dd class="font-semibold text-gray-800 dark:text-gray-100">
                        {{ $expense->approved_at?->format('M d, Y H:i') }}
                    </dd>
                </div>
                @endif
                @if($expense->description)
                <div class="col-span-2">
                    <dt class="text-gray-400 text-xs font-medium mb-0.5">Description</dt>
                    <dd class="text-gray-700 dark:text-gray-200">{{ $expense->description }}</dd>
                </div>
                @endif
                @if($expense->notes)
                <div class="col-span-2">
                    <dt class="text-gray-400 text-xs font-medium mb-0.5">Notes</dt>
                    <dd class="text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-white/[.04]
                               rounded-xl p-3">{{ $expense->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Actions sidebar --}}
        <div class="space-y-4">

            {{-- Approve / Reject --}}
            @if($expense->isPending())
            <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-5 space-y-3">
                <h2 class="font-bold text-gray-700 dark:text-gray-300 text-sm">Review</h2>

                @can('approve', $expense)
                <form method="POST" action="{{ route('admin.expenses.approve', $expense) }}"
                      onsubmit="return confirm('Approve this expense?')">
                    @csrf
                    <button type="submit"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold
                                   py-2.5 rounded-xl text-sm transition-colors">
                        ✅ Approve
                    </button>
                </form>
                @endcan

                @can('reject', $expense)
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold
                                   py-2.5 rounded-xl text-sm transition-colors">
                        ❌ Reject
                    </button>
                    <div x-show="open" x-cloak class="mt-3">
                        <form method="POST" action="{{ route('admin.expenses.reject', $expense) }}">
                            @csrf
                            <textarea name="notes" rows="3" required
                                      placeholder="Rejection reason (required)…"
                                      class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                                             bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                                             px-3 py-2 text-sm focus:outline-none focus:ring-2
                                             focus:ring-red-500 resize-none"></textarea>
                            @error('notes')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            <button type="submit"
                                    class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white font-bold
                                           py-2 rounded-xl text-sm transition-colors">
                                Confirm Rejection
                            </button>
                        </form>
                    </div>
                </div>
                @endcan
            </div>
            @endif

            {{-- Receipt --}}
            @if($expense->receipt_path)
            <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-5">
                <h2 class="font-bold text-gray-700 dark:text-gray-300 text-sm mb-3">Receipt</h2>
                <a href="{{ route('admin.expenses.receipt', $expense) }}" target="_blank"
                   class="flex items-center gap-2 text-sm text-[#6c63ff] hover:underline font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"/>
                    </svg>
                    View / Download Receipt
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
