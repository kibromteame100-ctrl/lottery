@extends('layouts.admin')
@section('title', 'Expenses')

@section('content')
<div class="space-y-5">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 dark:text-white">Expenses</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Track and manage operational expenses</p>
        </div>
        @can('create', \App\Models\Expense::class)
        <a href="{{ route('admin.expenses.create') }}"
           class="inline-flex items-center gap-2 bg-[#6c63ff] hover:bg-[#574fd6] text-white
                  font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Expense
        </a>
        @endcan
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $statCards = [
            ['label'=>'This Month',    'value'=> 'ETB '.number_format($stats['total_this_month'],2),   'color'=>'red',    'icon'=>'💸'],
            ['label'=>'Pending Review','value'=> number_format($stats['pending_count']),               'color'=>'yellow', 'icon'=>'⏳'],
            ['label'=>'Approved (Mo.)','value'=> number_format($stats['approved_this_month']),          'color'=>'green',  'icon'=>'✅'],
            ['label'=>'All-Time Total','value'=> 'ETB '.number_format($stats['total_all_time'],2),      'color'=>'blue',   'icon'=>'📊'],
        ];
        $cm = ['red'=>'ca-red','yellow'=>'ca-yellow','green'=>'ca-green','blue'=>'ca-blue'];
        $ibm= ['red'=>'ib-red','yellow'=>'ib-yellow','green'=>'ib-green','blue'=>'ib-blue'];
        @endphp
        @foreach($statCards as $c)
        <div class="relative bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card {{ $cm[$c['color']] }} overflow-hidden kpi-card p-5">
            <span class="m-ghost">{{ $c['icon'] }}</span>
            <div class="{{ $ibm[$c['color']] }} w-10 h-10 rounded-xl flex items-center justify-center text-xl mb-3">
                {{ $c['icon'] }}
            </div>
            <p class="text-2xl font-extrabold text-gray-900 dark:text-white leading-tight">{{ $c['value'] }}</p>
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1">{{ $c['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Category breakdown --}}
    @if($byCategory->count())
    <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-5">
        <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-4">This Month by Category</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($byCategory as $cat)
            <div class="bg-gray-50 dark:bg-white/[.05] rounded-xl p-3 border border-gray-100 dark:border-white/[.07]">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    {{ $categories[$cat->category] ?? ucfirst($cat->category) }}
                </p>
                <p class="text-base font-extrabold text-gray-900 dark:text-white mt-0.5">
                    ETB {{ number_format($cat->total, 2) }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.expenses.index') }}"
          class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-4">
        <div class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search title…"
                   class="flex-1 min-w-[160px] rounded-xl border border-gray-200 dark:border-white/[.1]
                          bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                          placeholder-gray-400 px-3 py-2 text-sm focus:outline-none focus:ring-2
                          focus:ring-[#6c63ff]">

            <select name="status"
                    class="rounded-xl border border-gray-200 dark:border-white/[.1]
                           bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                           px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
                <option value="">All Statuses</option>
                @foreach(['pending','approved','rejected'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>

            <select name="category"
                    class="rounded-xl border border-gray-200 dark:border-white/[.1]
                           bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                           px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
                <option value="">All Categories</option>
                @foreach($categories as $key => $label)
                <option value="{{ $key }}" @selected(request('category')===$key)>{{ $label }}</option>
                @endforeach
            </select>

            <input type="date" name="from" value="{{ request('from') }}"
                   class="rounded-xl border border-gray-200 dark:border-white/[.1]
                          bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                          px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
            <input type="date" name="to" value="{{ request('to') }}"
                   class="rounded-xl border border-gray-200 dark:border-white/[.1]
                          bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                          px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">

            <button type="submit"
                    class="bg-[#6c63ff] hover:bg-[#574fd6] text-white px-4 py-2 rounded-xl
                           text-sm font-semibold transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search','status','category','from','to']))
            <a href="{{ route('admin.expenses.index') }}"
               class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 px-2 py-2">Clear</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[640px]">
                <thead class="bg-gray-50 dark:bg-white/[.04]">
                    <tr class="text-left text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                        <th class="px-5 py-3">#</th>
                        <th class="px-5 py-3">Title</th>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Amount</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Created By</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[.04]">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[.03] transition-colors">
                        <td class="px-5 py-3 text-gray-400 text-xs">#{{ $expense->id }}</td>
                        <td class="px-5 py-3">
                            <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $expense->title }}</p>
                            @if($expense->description)
                            <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $expense->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs bg-gray-100 dark:bg-white/[.08] text-gray-600 dark:text-gray-300
                                         px-2 py-1 rounded-lg font-medium">
                                {{ $expense->category_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3 font-bold text-gray-900 dark:text-white text-sm whitespace-nowrap">
                            ETB {{ number_format($expense->amount, 2) }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $expense->expense_date->format('M d, Y') }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-600 dark:text-gray-300">
                            {{ $expense->creator?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                         {{ $expense->status_badge_class }}">
                                {{ ucfirst($expense->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2 flex-wrap">
                                {{-- View --}}
                                <a href="{{ route('admin.expenses.show', $expense) }}"
                                   class="text-[#6c63ff] hover:underline text-xs font-semibold">View</a>

                                {{-- Edit --}}
                                @can('update', $expense)
                                <a href="{{ route('admin.expenses.edit', $expense) }}"
                                   class="text-indigo-500 hover:underline text-xs font-semibold">Edit</a>
                                @endcan

                                {{-- Approve --}}
                                @can('approve', $expense)
                                <form method="POST" action="{{ route('admin.expenses.approve', $expense) }}"
                                      onsubmit="return confirm('Approve this expense?')">
                                    @csrf
                                    <button type="submit"
                                            class="text-emerald-600 hover:underline text-xs font-semibold">
                                        Approve
                                    </button>
                                </form>
                                @endcan

                                {{-- Delete --}}
                                @can('delete', $expense)
                                <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}"
                                      onsubmit="return confirm('Delete this expense?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-500 hover:underline text-xs font-semibold">
                                        Delete
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-400 dark:text-gray-600">
                            <div class="text-4xl mb-2 opacity-30">💸</div>
                            No expenses found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-white/[.05]">
            {{ $expenses->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
