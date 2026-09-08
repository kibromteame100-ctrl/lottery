@extends('layouts.admin')
@section('title', __('admin.dashboard'))

@push('styles')
<style>
    /* ── KPI card shine effect ─────────────────────────────── */
    .kpi-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: linear-gradient(135deg, rgba(255,255,255,.06) 0%, transparent 60%);
        pointer-events: none;
    }

    /* ── Gradient number text ──────────────────────────────── */
    .num-gradient {
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ── Pulse ring on pending badge ───────────────────────── */
    @keyframes ping-sm {
        75%,100% { transform: scale(1.8); opacity: 0; }
    }
    .badge-ping::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 9999px;
        background: inherit;
        animation: ping-sm .9s cubic-bezier(0,0,.2,1) infinite;
    }
</style>
@endpush

@section('content')
<div class="space-y-5 md:space-y-6">

{{-- ══════════════════════════════════════════════
     HERO BANNER
══════════════════════════════════════════════ --}}
<div class="hero-bg relative overflow-hidden rounded-2xl md:rounded-3xl text-white
            px-5 py-6 sm:px-8 sm:py-8 md:px-10 md:py-10 shadow-glow">

    {{-- Decorative orbs --}}
    <div class="hero-orb w-64 h-64 -top-16 -right-16"></div>
    <div class="hero-orb w-40 h-40 bottom-0 right-1/3 -translate-y-1/2 opacity-60"></div>
    <div class="hero-orb w-20 h-20 top-1/4 right-1/4 opacity-40"></div>
    <span class="hero-mark hidden md:block">🎰</span>

    <div class="relative z-10">
        <p class="flex items-center gap-1.5 text-white/60 text-xs sm:text-sm mb-2">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ now()->format('l, F j, Y') }}
        </p>

        <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold leading-tight mb-1.5">
            Welcome back, {{ auth()->user()->name }} 👋
        </h1>
        <p class="text-white/65 text-xs sm:text-sm mb-5 max-w-sm">
            Here's what's happening at <strong class="text-white font-bold">Lottery Platform</strong> today.
        </p>

        <div class="flex flex-wrap gap-2 sm:gap-3">
            {{-- Review Payments --}}
            <a href="{{ route('admin.tickets.index', ['status'=>'pending']) }}"
               class="inline-flex items-center gap-2 bg-white text-[#6c63ff] font-bold
                      text-xs sm:text-sm px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl
                      hover:bg-white/90 active:scale-95 transition-all shadow-md">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Review Payments
                @if($stats['pending_count'] > 0)
                <span class="bg-yellow-400 text-yellow-900 text-[10px] font-black
                             rounded-full px-1.5 py-0.5 leading-none">
                    {{ $stats['pending_count'] }}
                </span>
                @endif
            </a>

            @can('create', \App\Models\Lottery::class)
            <a href="{{ route('admin.lotteries.create') }}"
               class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25
                      text-white font-semibold text-xs sm:text-sm px-3 sm:px-4 py-2 sm:py-2.5
                      rounded-xl transition-all active:scale-95 border border-white/20">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Lottery
            </a>
            @endcan

            <a href="{{ route('admin.reports.index') }}"
               class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25
                      text-white font-semibold text-xs sm:text-sm px-3 sm:px-4 py-2 sm:py-2.5
                      rounded-xl transition-all active:scale-95 border border-white/20">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                View Reports
            </a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     KPI CARDS — 2 cols mobile / 3 cols tablet / 6 cols desktop
══════════════════════════════════════════════ --}}
@php
// Smart number formatter
// Returns ['prefix'=>'ETB', 'number'=>'1.23B', 'exact'=>'1,234,567,890.00']
function formatKpiValue($raw, string $prefix = '', bool $showExact = false): array {
    $abs = abs((float) $raw);
    if ($abs >= 1_000_000_000) {
        $formatted = rtrim(rtrim(number_format($abs / 1_000_000_000, 2), '0'), '.') . 'B';
    } elseif ($abs >= 1_000_000) {
        $formatted = rtrim(rtrim(number_format($abs / 1_000_000, 2), '0'), '.') . 'M';
    } elseif ($abs >= 1_000) {
        $formatted = rtrim(rtrim(number_format($abs / 1_000, 1), '0'), '.') . 'K';
    } else {
        $formatted = number_format($abs, is_float($raw + 0) ? 2 : 0);
    }
    // exact: always 2 decimal places for currency, 0 for counts
    $exact = $showExact ? number_format((float)$raw, 2) : null;
    return ['prefix' => $prefix, 'number' => $formatted, 'exact' => $exact];
}

$kpis = [
    [
        'accent'     => 'ca-yellow',
        'ib'         => 'ib-yellow',
        'icon_class' => 'text-yellow-500',
        'num_from'   => '#f59e0b',
        'num_to'     => '#d97706',
        'ghost'      => '⏳',
        'value'      => formatKpiValue($stats['pending_count']),
        'label'      => __('admin.pending_payments'),
        'sub_label'  => 'Awaiting review',
        'sub_class'  => 'text-yellow-500 dark:text-yellow-400',
        'link'       => route('admin.tickets.index', ['status'=>'pending']),
        'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ],
    [
        'accent'     => 'ca-green',
        'ib'         => 'ib-green',
        'icon_class' => 'text-emerald-500',
        'num_from'   => '#10b981',
        'num_to'     => '#059669',
        'ghost'      => '✅',
        'value'      => formatKpiValue($stats['approved_count']),
        'label'      => __('admin.approved_tickets'),
        'sub_label'  => 'Numbers generated',
        'sub_class'  => 'text-emerald-500 dark:text-emerald-400',
        'link'       => route('admin.tickets.index', ['status'=>'approved']),
        'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ],
    [
        'accent'     => 'ca-blue',
        'ib'         => 'ib-blue',
        'icon_class' => 'text-[#6c63ff]',
        'num_from'   => '#6c63ff',
        'num_to'     => '#4f46e5',
        'ghost'      => '💰',
        'value'      => formatKpiValue($stats['total_sales'], 'ETB', true),
        'label'      => __('admin.total_sales'),
        'sub_label'  => 'Total earnings',
        'sub_class'  => 'text-[#6c63ff] dark:text-[#8b85ff]',
        'link'       => route('admin.reports.index'),
        'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ],
    [
        'accent'     => 'ca-red',
        'ib'         => 'ib-red',
        'icon_class' => 'text-red-500',
        'num_from'   => '#ef4444',
        'num_to'     => '#dc2626',
        'ghost'      => '❌',
        'value'      => formatKpiValue($stats['rejected_count']),
        'label'      => __('admin.rejected_tickets'),
        'sub_label'  => 'View details',
        'sub_class'  => 'text-red-500 dark:text-red-400',
        'link'       => route('admin.tickets.index', ['status'=>'rejected']),
        'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ],
    [
        'accent'     => 'ca-indigo',
        'ib'         => 'ib-indigo',
        'icon_class' => 'text-indigo-500',
        'num_from'   => '#4f46e5',
        'num_to'     => '#4338ca',
        'ghost'      => '🎰',
        'value'      => formatKpiValue($stats['active_lotteries']),
        'label'      => __('admin.active_lotteries'),
        'sub_label'  => 'Manage lotteries',
        'sub_class'  => 'text-indigo-500 dark:text-indigo-400',
        'link'       => route('admin.lotteries.index'),
        'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
    ],
    [
        'accent'     => 'ca-cyan',
        'ib'         => 'ib-cyan',
        'icon_class' => 'text-cyan-500',
        'num_from'   => '#06b6d4',
        'num_to'     => '#0891b2',
        'ghost'      => '👥',
        'value'      => formatKpiValue($stats['total_users']),
        'label'      => __('admin.total_users'),
        'sub_label'  => 'All customers',
        'sub_class'  => 'text-cyan-500 dark:text-cyan-400',
        'link'       => route('admin.users.index'),
        'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
    ],
    [
        'accent'     => 'ca-red',
        'ib'         => 'ib-red',
        'icon_class' => 'text-red-500',
        'num_from'   => '#ef4444',
        'num_to'     => '#dc2626',
        'ghost'      => '💸',
        'value'      => formatKpiValue($stats['expenses_month'], 'ETB', true),
        'label'      => 'Expenses This Month',
        'sub_label'  => $stats['expenses_pending'] . ' pending review',
        'sub_class'  => 'text-red-500 dark:text-red-400',
        'link'       => route('admin.expenses.index'),
        'icon_svg'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>',
    ],
];
@endphp

<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
    @foreach($kpis as $kpi)
    <a href="{{ $kpi['link'] }}"
       class="kpi-card relative bg-white dark:bg-[#1a1a30] rounded-2xl overflow-hidden
              shadow-card {{ $kpi['accent'] }} block group">

        <span class="m-ghost">{{ $kpi['ghost'] }}</span>

        <div class="p-4 md:p-5 relative z-10">

            {{-- Icon bubble --}}
            <div class="w-10 h-10 md:w-11 md:h-11 rounded-2xl {{ $kpi['ib'] }}
                         flex items-center justify-center mb-3 md:mb-4
                         group-hover:scale-110 transition-transform duration-200">
                <svg class="w-5 h-5 {{ $kpi['icon_class'] }}" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    {!! $kpi['icon_svg'] !!}
                </svg>
            </div>

            {{-- Value: prefix badge + abbreviated number --}}
            <div class="flex items-start gap-1.5 mb-0.5 min-w-0">
                @if($kpi['value']['prefix'])
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md
                             text-[10px] font-bold shrink-0 mt-1 leading-none"
                      style="background:{{ $kpi['num_from'] }}22;
                             color:{{ $kpi['num_from'] }};
                             border:1px solid {{ $kpi['num_from'] }}44;">
                    {{ $kpi['value']['prefix'] }}
                </span>
                @endif
                <p class="font-extrabold leading-none num-gradient truncate"
                   style="font-size:clamp(1.35rem,2.8vw,1.85rem);
                          background-image:linear-gradient(135deg,{{ $kpi['num_from'] }},{{ $kpi['num_to'] }})">
                    {{ $kpi['value']['number'] }}
                </p>
            </div>

            {{-- Exact full amount (only for currency cards) --}}
            @if($kpi['value']['exact'])
            <p class="text-gray-400 dark:text-gray-500 leading-tight mb-1 break-all"
               style="font-size:11px;">
                {{ $kpi['value']['prefix'] }} {{ $kpi['value']['exact'] }}
            </p>
            @endif

            {{-- Label --}}
            <p class="text-[11px] md:text-xs font-semibold text-gray-500 dark:text-gray-400
                       leading-tight mb-2.5">
                {{ $kpi['label'] }}
            </p>

            {{-- Sub action --}}
            <span class="inline-flex items-center gap-0.5 text-[11px] font-semibold
                          {{ $kpi['sub_class'] }} group-hover:underline">
                {{ $kpi['sub_label'] }}
                <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </div>
    </a>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════
     CHARTS ROW — Sales line + Status donut
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4 md:gap-5">

    {{-- Sales Trend (3 / 5) --}}
    <div class="lg:col-span-3 bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card overflow-hidden">
        <div class="flex items-start justify-between px-5 py-4
                    border-b border-gray-100 dark:border-white/[.06]">
            <div>
                <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm">
                    Sales Trend — Last 30 Days
                </h2>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                    Approved ticket revenue
                </p>
            </div>
            <span class="shrink-0 text-xs font-bold text-[#6c63ff] dark:text-[#8b85ff]
                          bg-[#6c63ff]/10 dark:bg-[#6c63ff]/20 px-2.5 py-1.5 rounded-xl">
                ETB {{ number_format(array_sum($salesData)) }}
            </span>
        </div>
        <div class="p-4" style="height:220px; position:relative">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Status Donut (2 / 5) --}}
    <div class="lg:col-span-2 bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-white/[.06]">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm">Ticket Status</h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">All-time breakdown</p>
        </div>
        <div class="p-4 flex flex-col items-center" style="height:220px; position:relative">
            <canvas id="statusDonut" style="max-height:145px"></canvas>
            <div class="flex flex-wrap items-center justify-center gap-3 mt-2 text-[11px]">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block shrink-0"></span>
                    <span class="text-gray-600 dark:text-gray-300 font-medium">
                        Pending <strong>{{ $stats['pending_count'] }}</strong>
                    </span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block shrink-0"></span>
                    <span class="text-gray-600 dark:text-gray-300 font-medium">
                        Approved <strong>{{ $stats['approved_count'] }}</strong>
                    </span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block shrink-0"></span>
                    <span class="text-gray-600 dark:text-gray-300 font-medium">
                        Rejected <strong>{{ $stats['rejected_count'] }}</strong>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     LOTTERY COMPARISON — full width bar chart
══════════════════════════════════════════════ --}}
<div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2
                px-5 py-4 border-b border-gray-100 dark:border-white/[.06]">
        <div>
            <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm">
                Lottery Comparison
            </h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                Tickets by status across all lotteries
            </p>
        </div>
        <a href="{{ route('admin.lotteries.index') }}"
           class="text-xs text-[#6c63ff] dark:text-[#8b85ff] hover:underline font-bold
                  self-start sm:self-auto shrink-0">
            View all lotteries →
        </a>
    </div>

    {{-- Lottery stat pills --}}
    @if($lotteryChart->count())
    <div class="px-5 pt-4 flex flex-wrap gap-2">
        @foreach($lotteryChart as $lc)
        <div class="inline-flex items-center gap-2 bg-gray-50 dark:bg-white/[.05]
                     rounded-xl px-3 py-2 border border-gray-100 dark:border-white/[.07]">
            <span class="text-xs font-bold text-gray-800 dark:text-gray-200">
                {{ Str::limit($lc->name, 22) }}
            </span>
            <span class="text-[11px] text-gray-400">{{ $lc->total_count }} tickets</span>
            <span class="text-[11px] text-emerald-500 font-semibold">
                ETB {{ number_format($lc->total_revenue, 0) }}
            </span>
        </div>
        @endforeach
    </div>
    @endif

    <div class="p-4" style="height:260px; position:relative">
        <canvas id="lotteryBarChart"></canvas>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     BOTTOM — Recent tickets + Recent users
══════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-5">

    {{-- Recent Tickets (2/3) --}}
    <div class="xl:col-span-2 bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4
                    border-b border-gray-100 dark:border-white/[.06]">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm">
                {{ __('admin.recent_activity') }}
            </h2>
            <a href="{{ route('admin.tickets.index') }}"
               class="text-xs text-[#6c63ff] dark:text-[#8b85ff] hover:underline font-bold">
                View all →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[480px]">
                <thead class="bg-gray-50 dark:bg-white/[.04]">
                    <tr class="text-left text-[10px] font-bold text-gray-400 dark:text-gray-500
                               uppercase tracking-widest">
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Lottery</th>
                        <th class="px-5 py-3">Amount</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[.04]">
                    @forelse($recentTickets as $ticket)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-white/[.03] transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full text-white text-xs font-bold
                                             uppercase flex items-center justify-center shrink-0"
                                     style="background:linear-gradient(135deg,#6c63ff,#4f46e5)">
                                    {{ substr($ticket->user?->name ?? '?', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.tickets.show', $ticket) }}"
                                       class="font-semibold text-xs text-gray-800 dark:text-gray-100
                                              hover:text-[#6c63ff] block truncate max-w-[110px]">
                                        {{ $ticket->user?->display_name ?? '—' }}
                                    </a>
                                    <span class="text-[10px] text-gray-400 block truncate max-w-[110px]">
                                        {{ $ticket->user?->phone ?? $ticket->user?->email ?? '' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-600 dark:text-gray-300 max-w-[100px] truncate">
                            {{ $ticket->lottery?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-xs font-bold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                            {{ number_format($ticket->ticket_price, 2) }}
                        </td>
                        <td class="px-5 py-3">
                            @include('components.status-badge', ['status' => $ticket->status])
                        </td>
                        <td class="px-5 py-3 text-[10px] text-gray-400 whitespace-nowrap">
                            {{ $ticket->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400 dark:text-gray-600 text-sm">
                            <div class="text-3xl mb-2 opacity-30">🎟️</div>
                            {{ __('admin.no_records') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Users (1/3) --}}
    <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4
                    border-b border-gray-100 dark:border-white/[.06]">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm">
                Recent Registrations
            </h2>
            <a href="{{ route('admin.users.index') }}"
               class="text-xs text-[#6c63ff] dark:text-[#8b85ff] hover:underline font-bold">
                View all →
            </a>
        </div>

        {{-- Mini stats --}}
        @php
        $mStats = [
            ['label'=>'Today','value'=>\App\Models\TicketPurchase::whereDate('created_at',today())->count()],
            ['label'=>'Week', 'value'=>\App\Models\TicketPurchase::whereBetween('created_at',[now()->startOfWeek(),now()->endOfWeek()])->count()],
            ['label'=>'Month','value'=>\App\Models\TicketPurchase::whereMonth('created_at',now()->month)->whereYear('created_at',now()->year)->count()],
        ];
        @endphp
        <div class="grid grid-cols-3 border-b border-gray-100 dark:border-white/[.05]">
            @foreach($mStats as $ms)
            <div class="px-2 py-3 text-center {{ !$loop->last ? 'border-r border-gray-100 dark:border-white/[.05]' : '' }}">
                <p class="text-lg font-extrabold text-gray-900 dark:text-white leading-none">
                    {{ $ms['value'] }}
                </p>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wide mt-1">
                    {{ $ms['label'] }}
                </p>
            </div>
            @endforeach
        </div>

        <div class="divide-y divide-gray-100 dark:divide-white/[.04]">
            @forelse($recentUsers as $user)
            <div class="flex items-center gap-3 px-5 py-3
                         hover:bg-gray-50/80 dark:hover:bg-white/[.03] transition-colors">
                <div class="w-9 h-9 rounded-full text-white text-sm font-bold uppercase
                             flex items-center justify-center shrink-0"
                     style="background:linear-gradient(135deg,#6c63ff,#4f46e5)">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 truncate leading-tight">
                        {{ $user->name }}
                    </p>
                    <p class="text-[11px] text-gray-400 truncate">
                        {{ $user->phone ?? $user->email }}
                    </p>
                </div>
                <div class="text-right shrink-0">
                    @include('components.status-badge', ['status' => $user->status])
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-gray-400 dark:text-gray-600 text-sm">
                <div class="text-3xl mb-2 opacity-30">👥</div>
                {{ __('admin.no_records') }}
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     PENDING ALERT
══════════════════════════════════════════════ --}}
@if($stats['pending_count'] > 0)
<div class="bg-gradient-to-r from-yellow-50 to-orange-50
            dark:from-yellow-900/15 dark:to-orange-900/15
            border border-yellow-200 dark:border-yellow-700/40
            rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row
            items-start sm:items-center justify-between gap-4 shadow-sm">
    <div class="flex items-center gap-4">
        <div class="relative w-12 h-12 rounded-2xl bg-yellow-100 dark:bg-yellow-900/40
                     flex items-center justify-center shrink-0 text-2xl">
            ⚠️
        </div>
        <div>
            <p class="font-bold text-yellow-800 dark:text-yellow-200 text-sm">
                {{ $stats['pending_count'] }} payment {{ $stats['pending_count'] === 1 ? 'request' : 'requests' }}
                awaiting review
            </p>
            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-0.5">
                Customers are waiting for their lottery numbers.
            </p>
        </div>
    </div>
    <a href="{{ route('admin.tickets.index', ['status'=>'pending']) }}"
       class="shrink-0 bg-yellow-500 hover:bg-yellow-600 active:scale-95 text-white
              text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow whitespace-nowrap">
        Review Now →
    </a>
</div>
@endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Helpers ───────────────────────────────────────────────────────────────────
const dark      = () => document.documentElement.classList.contains('dark');
const gridClr   = () => dark() ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.06)';
const tickClr   = () => dark() ? '#6b7280' : '#9ca3af';
const lblClr    = () => dark() ? '#9ca3af' : '#6b7280';
const tooltipStyle = () => ({
    backgroundColor: dark() ? '#1e1e3a' : '#fff',
    titleColor:      dark() ? '#f3f4f6' : '#111827',
    bodyColor:       dark() ? '#9ca3af' : '#6b7280',
    borderColor:     dark() ? 'rgba(255,255,255,.1)' : '#e5e7eb',
    borderWidth: 1, padding: 10, cornerRadius: 10,
});

Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.font.size   = 11;

// ── 1. Sales line chart ───────────────────────────────────────────────────────
const salesLabels = @json($salesLabels);
const salesData   = @json($salesData);
const sCtx = document.getElementById('salesChart').getContext('2d');

function makeSalesGradient() {
    const g = sCtx.createLinearGradient(0, 0, 0, 180);
    g.addColorStop(0, 'rgba(108,99,255,.25)');
    g.addColorStop(1, 'rgba(108,99,255,.00)');
    return g;
}

const salesChart = new Chart(sCtx, {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets: [{
            data: salesData,
            borderColor: '#6c63ff',
            backgroundColor: makeSalesGradient(),
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#6c63ff',
            pointBorderColor: dark() ? '#1a1a30' : '#fff',
            pointBorderWidth: 2,
            tension: 0.42,
            fill: true,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { mode:'index', intersect:false },
        plugins: {
            legend: { display:false },
            tooltip: { ...tooltipStyle(),
                callbacks: { label: ctx => ' ETB ' + ctx.parsed.y.toLocaleString(undefined,{minimumFractionDigits:2}) }
            }
        },
        scales: {
            x: {
                grid: { color: gridClr(), drawBorder:false },
                ticks: { color: tickClr(), maxTicksLimit:7, maxRotation:0 },
                border: { display:false }
            },
            y: {
                grid: { color: gridClr(), drawBorder:false },
                ticks: { color: tickClr(), callback: v => v>=1000 ? (v/1000).toFixed(1)+'k' : v },
                border: { display:false },
                beginAtZero: true,
            }
        }
    }
});

// ── 2. Status donut ───────────────────────────────────────────────────────────
const dCtx = document.getElementById('statusDonut').getContext('2d');
const donutChart = new Chart(dCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending','Approved','Rejected'],
        datasets: [{
            data: [{{ $stats['pending_count'] }}, {{ $stats['approved_count'] }}, {{ $stats['rejected_count'] }}],
            backgroundColor: ['#f59e0b','#10b981','#ef4444'],
            borderColor: dark() ? '#1a1a30' : '#fff',
            borderWidth: 3,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout:'72%',
        plugins: {
            legend: { display:false },
            tooltip: { ...tooltipStyle() }
        }
    }
});

// ── 3. Lottery bar chart ──────────────────────────────────────────────────────
const lNames    = @json($lotteryNames);
const lPending  = @json($lotteryPending);
const lApproved = @json($lotteryApproved);
const lRejected = @json($lotteryRejected);
const lRevenue  = @json($lotteryRevenue);

const bCtx = document.getElementById('lotteryBarChart').getContext('2d');
const barChart = new Chart(bCtx, {
    type: 'bar',
    data: {
        labels: lNames,
        datasets: [
            { label:'Approved', data:lApproved, backgroundColor:'rgba(16,185,129,.85)',  borderRadius:5, borderSkipped:false },
            { label:'Pending',  data:lPending,  backgroundColor:'rgba(245,158,11,.85)',  borderRadius:5, borderSkipped:false },
            { label:'Rejected', data:lRejected, backgroundColor:'rgba(239,68,68,.85)',   borderRadius:5, borderSkipped:false },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        interaction: { mode:'index', intersect:false },
        plugins: {
            legend: {
                display: true, position:'top', align:'end',
                labels: {
                    color: lblClr(), boxWidth:10, boxHeight:10,
                    usePointStyle:true, pointStyle:'rectRounded',
                    padding:16, font:{ size:11, weight:'600' }
                }
            },
            tooltip: { ...tooltipStyle(),
                callbacks: {
                    afterBody: items => {
                        const i = items[0]?.dataIndex ?? 0;
                        return ['Revenue: ETB ' + (lRevenue[i]||0).toLocaleString(undefined,{minimumFractionDigits:2})];
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display:false },
                ticks: { color: tickClr() },
                border: { display:false }
            },
            y: {
                grid: { color: gridClr(), drawBorder:false },
                ticks: { color: tickClr(), precision:0, callback: v => Number.isInteger(v) ? v : '' },
                border: { display:false },
                beginAtZero: true,
            }
        }
    }
});

// ── Theme observer — update all charts live ───────────────────────────────────
new MutationObserver(() => {
    [salesChart, donutChart, barChart].forEach(chart => {
        const tp = chart.options.plugins.tooltip;
        Object.assign(tp, tooltipStyle());
        if (chart.options.scales?.x) {
            chart.options.scales.x.grid.color   = gridClr();
            chart.options.scales.x.ticks.color  = tickClr();
        }
        if (chart.options.scales?.y) {
            chart.options.scales.y.grid.color   = gridClr();
            chart.options.scales.y.ticks.color  = tickClr();
        }
        if (chart.options.plugins.legend?.labels) {
            chart.options.plugins.legend.labels.color = lblClr();
        }
    });

    // Donut border
    donutChart.data.datasets[0].borderColor = dark() ? '#1a1a30' : '#fff';

    // Sales gradient
    salesChart.data.datasets[0].backgroundColor = makeSalesGradient();
    salesChart.data.datasets[0].pointBorderColor = dark() ? '#1a1a30' : '#fff';

    [salesChart, donutChart, barChart].forEach(c => c.update('none'));

}).observe(document.documentElement, { attributes:true, attributeFilter:['class'] });
</script>
@endpush
