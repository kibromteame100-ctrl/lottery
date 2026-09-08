<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="{{ session('theme', auth()->user()?->theme_preference ?? 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.dashboard')) — Lottery Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter','sans-serif'] },
                    colors: {
                        brand: { DEFAULT:'#6c63ff', hover:'#574fd6', light:'#8b85ff' }
                    },
                    boxShadow: {
                        card: '0 2px 12px rgba(0,0,0,0.06)',
                        'card-hover': '0 8px 24px rgba(0,0,0,0.10)',
                        glow: '0 4px 20px rgba(108,99,255,0.35)',
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *,*::before,*::after{box-sizing:border-box}
        [x-cloak]{display:none!important}

        /* ── Sidebar scrollbar ─────────── */
        .sb-scroll::-webkit-scrollbar{width:3px}
        .sb-scroll::-webkit-scrollbar-track{background:transparent}
        .sb-scroll::-webkit-scrollbar-thumb{background:#2d2d4e;border-radius:4px}

        /* ── Sidebar slide ──────────────────────────── */
        .sb-slide{transition:transform .25s cubic-bezier(.4,0,.2,1)}

        /* ── Nav pill active ────────────────────────── */
        .nav-pill{
            background:linear-gradient(90deg,#6c63ff,#574fd6);
            box-shadow:0 4px 16px rgba(108,99,255,.4);
            color:#fff!important;
        }
        .nav-item{transition:background .15s,color .15s,transform .1s}
        .nav-item:active{transform:scale(.98)}

        /* ── Card accent borders ────────────────────── */
        .ca-yellow{border-top:3px solid #f59e0b}
        .ca-green {border-top:3px solid #10b981}
        .ca-blue  {border-top:3px solid #6c63ff}
        .ca-red   {border-top:3px solid #ef4444}
        .ca-indigo{border-top:3px solid #4f46e5}
        .ca-cyan  {border-top:3px solid #06b6d4}

        /* ── Card hover lift ────────────────────────── */
        .kpi-card{
            transition:transform .2s ease,box-shadow .2s ease;
            cursor:default;
        }
        .kpi-card:hover{
            transform:translateY(-3px);
            box-shadow:0 12px 28px rgba(0,0,0,.10);
        }
        .dark .kpi-card:hover{
            box-shadow:0 12px 28px rgba(0,0,0,.35);
        }

        /* ── Ghost metric icon ──────────────────────── */
        .m-ghost{
            position:absolute;right:.75rem;bottom:.25rem;
            font-size:58px;line-height:1;
            opacity:.05;pointer-events:none;user-select:none;
        }

        /* ── Hero gradient ──────────────────────────── */
        .hero-bg{
            background:linear-gradient(135deg,#6c63ff 0%,#574fd6 55%,#4338ca 100%);
        }
        .hero-orb{
            position:absolute;border-radius:50%;background:rgba(255,255,255,.06);
            pointer-events:none;
        }
        .hero-mark{
            position:absolute;right:2rem;top:50%;
            transform:translateY(-50%);
            font-size:150px;line-height:1;
            opacity:.07;pointer-events:none;user-select:none;
        }

        /* ── Smooth scroll ──────────────────────────── */
        html{scroll-behavior:smooth}

        /* ── Icon bubble colours ────────────────────── */
        .ib-yellow{background:rgba(245,158,11,.13)}
        .ib-green {background:rgba(16,185,129,.13)}
        .ib-blue  {background:rgba(108,99,255,.13)}
        .ib-red   {background:rgba(239,68,68,.13)}
        .ib-indigo{background:rgba(79,70,229,.13)}
        .ib-cyan  {background:rgba(6,182,212,.13)}

        /* ── Collapsible nav group arrow ────────────── */
        .grp-arrow{transition:transform .2s}
        .grp-open .grp-arrow{transform:rotate(180deg)}
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-[#0f0f1a] min-h-screen font-sans antialiased"
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

{{-- ═══════════════════════════════════════
     MOBILE OVERLAY
═══════════════════════════════════════ --}}
<div x-show="sidebarOpen"
     @click="sidebarOpen=false"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-20 lg:hidden"
     x-cloak
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

{{-- ═══════════════════════════════════════
     SIDEBAR
═══════════════════════════════════════ --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="sb-slide fixed top-0 left-0 h-screen w-[260px] z-30 flex flex-col
              bg-[#13132b] dark:bg-[#0d0d1f] text-white select-none
              lg:translate-x-0 overflow-hidden border-r border-white/[.04]">

    {{-- Brand -------------------------------------------------- --}}
    <div class="flex items-center gap-3 px-5 py-[18px] border-b border-white/[.05] shrink-0">
        <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 text-xl"
             style="background:linear-gradient(135deg,#6c63ff,#4f46e5);
                    box-shadow:0 4px 14px rgba(108,99,255,.45)">
            🎰
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-bold text-[14px] tracking-wide leading-tight text-white">
                Lottery Platform
            </p>
            <p class="text-[10px] text-slate-500 uppercase tracking-[.12em] mt-[2px]">
                Admin System
            </p>
        </div>
        {{-- collapse toggle (desktop) --}}
        <button @click="sidebarOpen=false"
                class="w-6 h-6 rounded-full bg-white/10 hover:bg-white/20 text-slate-400
                       hover:text-white flex items-center justify-center text-xs
                       transition-colors lg:hidden" aria-label="Close">
            ✕
        </button>
    </div>

    {{-- Search -------------------------------------------------- --}}
    <div class="px-4 pt-3 pb-1 shrink-0">
        <label class="flex items-center gap-2 bg-white/[.05] hover:bg-white/[.08]
                       rounded-xl px-3 py-[9px] transition-colors cursor-text">
            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="Quick search…"
                   class="bg-transparent text-xs text-slate-400 placeholder-slate-600
                          focus:outline-none focus:placeholder-slate-500 w-full">
        </label>
    </div>

    {{-- Navigation --------------------------------------------- --}}
    <nav class="flex-1 overflow-y-auto sb-scroll px-3 py-2 space-y-0.5">

        @php
        $user         = auth()->user();
        $pendingCount = \App\Models\TicketPurchase::pending()->count();

        $navGroups = [
            // ── No label ──────────────────────────────────────────
            [
                'label' => null,
                'items' => [
                    [
                        'route' => 'admin.dashboard',
                        'icon'  => '🏠', 'color' => '#6c63ff',
                        'label' => __('admin.dashboard'),
                        'roles' => [], 'badge' => null,
                    ],
                ],
            ],
            // ── MANAGEMENT ────────────────────────────────────────
            [
                'label' => 'MANAGEMENT',
                'items' => [
                    [
                        'route' => 'admin.tickets.index',
                        'icon'  => '🎟️', 'color' => '#f59e0b',
                        'label' => __('admin.tickets'),
                        'roles' => ['super-admin','payment-reviewer','report-viewer'],
                        'badge' => 'pending',
                    ],
                    [
                        'route' => 'admin.lotteries.index',
                        'icon'  => '🎰', 'color' => '#10b981',
                        'label' => __('admin.lotteries'),
                        'roles' => ['super-admin','report-viewer'],
                        'badge' => null,
                    ],
                    [
                        'route' => 'admin.expenses.index',
                        'icon'  => '💸', 'color' => '#ef4444',
                        'label' => 'Expenses',
                        'roles' => ['super-admin','payment-reviewer','report-viewer'],
                        'badge' => null,
                    ],
                    // ── Users submenu ────────────────────────────
                    [
                        'type'  => 'submenu',
                        'icon'  => '👥', 'color' => '#06b6d4',
                        'label' => 'Users',
                        'roles' => ['super-admin','report-viewer'],
                        'children' => [
                            [
                                'route' => 'admin.users.index',
                                'icon'  => '👤',
                                'label' => 'Customers',
                                'roles' => ['super-admin','report-viewer'],
                            ],
                            [
                                'route' => 'admin.users.admins',
                                'icon'  => '🛡️',
                                'label' => 'Admin Users',
                                'roles' => ['super-admin'],
                            ],
                            [
                                'route' => 'admin.roles.index',
                                'icon'  => '🔑',
                                'label' => 'Roles & Permissions',
                                'roles' => ['super-admin'],
                            ],
                        ],
                    ],
                ],
            ],
            // ── REPORTS ───────────────────────────────────────────
            [
                'label' => 'REPORTS',
                'items' => [
                    [
                        'route' => 'admin.reports.index',
                        'icon'  => '📊', 'color' => '#a855f7',
                        'label' => __('admin.reports'),
                        'roles' => ['super-admin','report-viewer'],
                        'badge' => null,
                    ],
                    [
                        'route' => 'admin.audit-logs.index',
                        'icon'  => '📋', 'color' => '#64748b',
                        'label' => __('admin.audit_logs'),
                        'roles' => ['super-admin'],
                        'badge' => null,
                    ],
                ],
            ],
            // ── SYSTEM ────────────────────────────────────────────
            [
                'label' => 'SYSTEM',
                'items' => [
                    [
                        'route' => 'admin.settings.index',
                        'icon'  => '⚙️', 'color' => '#6b7280',
                        'label' => __('admin.settings'),
                        'roles' => ['super-admin'],
                        'badge' => null,
                    ],
                ],
            ],
        ];
        @endphp

        @foreach($navGroups as $group)

            {{-- ── Section label ──────────────────────────────── --}}
            @if($group['label'])
            <div x-data="{ open: true }" class="mt-1" :class="open ? 'grp-open' : ''">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-3 py-1.5
                               text-[10px] font-bold text-slate-600 uppercase tracking-[.12em]
                               hover:text-slate-400 transition-colors">
                    <span>{{ $group['label'] }}</span>
                    <svg class="grp-arrow w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"
                     x-transition:enter="transition-all duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-0.5 mt-0.5">

                    @foreach($group['items'] as $item)

                        {{-- ── Submenu parent ───────────────────── --}}
                        @if(($item['type'] ?? '') === 'submenu')
                            @if(empty($item['roles']) || $user->hasAnyRole($item['roles']))
                            @php
                                $submenuActive = false;
                                foreach ($item['children'] as $child) {
                                    if (request()->routeIs($child['route'] . '*')) {
                                        $submenuActive = true; break;
                                    }
                                }
                                // auto-open if a child is active
                            @endphp
                            <div x-data="{ open: {{ $submenuActive ? 'true' : 'false' }} }">

                                {{-- Parent trigger --}}
                                <button @click="open = !open"
                                        class="nav-item w-full flex items-center gap-3 px-3 py-[9px]
                                               rounded-xl text-[13px] font-medium transition-colors
                                               {{ $submenuActive
                                                  ? 'text-white bg-white/[.06]'
                                                  : 'text-slate-400 hover:bg-white/[.06] hover:text-slate-200' }}">
                                    <span class="w-8 h-8 rounded-xl flex items-center justify-center
                                                 shrink-0 text-[15px]"
                                          style="background:rgba({{ implode(',', sscanf(ltrim($item['color'],'#'),'%02x%02x%02x')) }},.18)">
                                        {{ $item['icon'] }}
                                    </span>
                                    <span class="flex-1 text-left leading-tight">{{ $item['label'] }}</span>
                                    {{-- Chevron --}}
                                    <svg :class="open ? 'rotate-180' : ''"
                                         class="w-3.5 h-3.5 transition-transform duration-200 text-slate-500"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                {{-- Children --}}
                                <div x-show="open"
                                     x-transition:enter="transition-all duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-0.5 ml-4 pl-3 space-y-0.5
                                            border-l border-white/[.07]">
                                    @foreach($item['children'] as $child)
                                        @if(empty($child['roles']) || $user->hasAnyRole($child['roles']))
                                        @php $childActive = request()->routeIs($child['route'] . '*'); @endphp
                                        <a href="{{ route($child['route']) }}"
                                           class="nav-item flex items-center gap-2.5 px-3 py-2
                                                  rounded-xl text-[12.5px] font-medium transition-colors
                                                  {{ $childActive
                                                     ? 'nav-pill'
                                                     : 'text-slate-500 hover:bg-white/[.06] hover:text-slate-200' }}">
                                            <span class="text-[13px] leading-none shrink-0">
                                                {{ $child['icon'] }}
                                            </span>
                                            <span class="leading-tight">{{ $child['label'] }}</span>
                                        </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif

                        {{-- ── Regular nav item ─────────────────── --}}
                        @else
                            @if(empty($item['roles']) || $user->hasAnyRole($item['roles']))
                            @php $active = request()->routeIs($item['route'] . '*'); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="nav-item flex items-center gap-3 px-3 py-[9px] rounded-xl
                                      text-[13px] font-medium
                                      {{ $active
                                         ? 'nav-pill'
                                         : 'text-slate-400 hover:bg-white/[.06] hover:text-slate-200' }}">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center
                                             shrink-0 text-[15px]"
                                      style="background:{{ $active
                                          ? 'rgba(255,255,255,.18)'
                                          : 'rgba('.implode(',',sscanf(ltrim($item['color'],'#'),'%02x%02x%02x')).',.18)' }}">
                                    {{ $item['icon'] }}
                                </span>
                                <span class="flex-1 leading-tight">{{ $item['label'] }}</span>
                                @if(($item['badge'] ?? null) === 'pending' && $pendingCount > 0)
                                <span class="text-[10px] font-bold bg-yellow-400 text-yellow-900
                                             rounded-full px-1.5 py-0.5 leading-none">
                                    {{ $pendingCount }}
                                </span>
                                @endif
                            </a>
                            @endif
                        @endif

                    @endforeach
                </div>
            </div>

            {{-- ── No-label group (Dashboard) ─────────────────── --}}
            @else
                @foreach($group['items'] as $item)
                    @if(empty($item['roles']) || $user->hasAnyRole($item['roles']))
                    @php $active = request()->routeIs($item['route'] . '*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="nav-item flex items-center gap-3 px-3 py-[9px] rounded-xl
                              text-[13px] font-medium
                              {{ $active
                                 ? 'nav-pill'
                                 : 'text-slate-400 hover:bg-white/[.06] hover:text-slate-200' }}">
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-[15px]"
                              style="background:{{ $active ? 'rgba(255,255,255,.18)' : 'rgba(108,99,255,.18)' }}">
                            {{ $item['icon'] }}
                        </span>
                        <span class="flex-1 leading-tight">{{ $item['label'] }}</span>
                    </a>
                    @endif
                @endforeach
            @endif

        @endforeach

    </nav>

    {{-- User footer -------------------------------------------- --}}
    <div class="border-t border-white/[.05] px-4 py-4 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                         font-bold text-sm text-white uppercase"
                 style="background:linear-gradient(135deg,#6c63ff,#4f46e5);
                        box-shadow:0 2px 8px rgba(108,99,255,.4)">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-semibold text-white truncate leading-tight">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-[11px] text-slate-500 truncate capitalize leading-tight mt-[1px]">
                    {{ auth()->user()->getRoleNames()->first() ?? 'Admin' }}
                </p>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                        class="w-7 h-7 flex items-center justify-center rounded-lg
                               text-slate-600 hover:text-red-400 hover:bg-white/10
                               transition-colors"
                        title="{{ __('admin.logout') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ═══════════════════════════════════════
     MAIN WRAPPER
═══════════════════════════════════════ --}}
<div class="lg:pl-[260px] min-h-screen flex flex-col">

    {{-- ── TOP BAR ────────────────────────────────── --}}
    <header class="sticky top-0 z-40 bg-white dark:bg-[#16162e]
                   border-b border-gray-200 dark:border-white/[.06]
                   shadow-md"
            style="backdrop-filter:none;-webkit-backdrop-filter:none;">
        <div class="flex items-center justify-between px-4 md:px-6 h-14">

            {{-- Left --}}
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen=!sidebarOpen"
                        class="w-9 h-9 flex items-center justify-center rounded-xl
                               text-gray-500 dark:text-gray-400
                               hover:bg-gray-100 dark:hover:bg-white/[.07] lg:hidden
                               transition-colors"
                        aria-label="Toggle menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <nav class="hidden sm:flex items-center gap-1.5 text-sm">
                    <a href="{{ route('admin.dashboard') }}"
                       class="text-gray-400 dark:text-gray-500 hover:text-[#6c63ff]
                              flex items-center gap-1.5 transition-colors whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        <span>Home</span>
                    </a>
                    <span class="text-gray-300 dark:text-gray-700 select-none">/</span>
                    <span class="text-gray-800 dark:text-gray-100 font-semibold text-sm truncate max-w-[180px]">
                        @yield('title','Dashboard')
                    </span>
                </nav>
            </div>

            {{-- Right --}}
            <div class="flex items-center gap-1 md:gap-2">

                {{-- Language --}}
                <div class="relative" x-data="{ open:false }">
                    <button @click="open=!open"
                            class="text-xs px-2.5 py-1.5 rounded-lg border
                                   border-gray-200 dark:border-white/[.1]
                                   bg-white dark:bg-white/[.06]
                                   text-gray-600 dark:text-gray-300
                                   hover:bg-gray-50 dark:hover:bg-white/[.1]
                                   font-bold uppercase tracking-wide transition-colors">
                        {{ app()->getLocale() }}
                    </button>
                    <div x-show="open" @click.away="open=false" x-cloak
                         class="absolute right-0 mt-1.5 w-32 bg-white dark:bg-[#1e1e3a] shadow-xl
                                rounded-xl border border-gray-100 dark:border-white/[.08]
                                z-50 overflow-hidden text-sm">
                        @foreach(['en'=>'English','am'=>'አማርኛ','ti'=>'ትግርኛ'] as $code => $label)
                        <form method="POST" action="{{ route('admin.set-locale') }}">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $code }}">
                            <button type="submit"
                                    class="w-full text-left px-4 py-2.5 transition-colors
                                           text-gray-700 dark:text-gray-300
                                           hover:bg-gray-50 dark:hover:bg-white/[.07]
                                           {{ app()->getLocale()===$code
                                              ? 'font-bold text-[#6c63ff] dark:text-[#8b85ff]' : '' }}">
                                {{ $label }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>

                {{-- Theme --}}
                <form method="POST" action="{{ route('admin.set-theme') }}">
                    @csrf
                    <input type="hidden" name="theme"
                           value="{{ session('theme', auth()->user()?->theme_preference ?? 'light') === 'dark' ? 'light' : 'dark' }}">
                    <button type="submit"
                            class="w-9 h-9 flex items-center justify-center rounded-xl transition-colors
                                   text-gray-500 dark:text-yellow-400
                                   hover:bg-gray-100 dark:hover:bg-white/[.07]"
                            aria-label="Toggle theme">
                        @if(session('theme', auth()->user()?->theme_preference ?? 'light') === 'dark')
                        <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zm4.5 2.692a.75.75 0 01.75.75v.008a.75.75 0 01-1.5 0v-.008a.75.75 0 01.75-.75zm2.56 2.56a.75.75 0 011.06 0l1.591 1.591a.75.75 0 01-1.06 1.061l-1.591-1.59a.75.75 0 010-1.062zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zm-2.56 4.5a.75.75 0 011.06 1.06l-1.59 1.591a.75.75 0 01-1.061-1.06l1.59-1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zm-4.5-1.19a.75.75 0 011.061 1.06L7.07 19.461a.75.75 0 01-1.061-1.06l1.59-1.591zm-4.19-4.06a.75.75 0 010-1.5H5.56a.75.75 0 010 1.5H3.31zm1.128-5.25a.75.75 0 01.75-.75H7.5a.75.75 0 010 1.5H5.19a.75.75 0 01-.75-.75zm.75 12a.75.75 0 01-.75-.75V18a.75.75 0 011.5 0v1.5a.75.75 0 01-.75.75zM12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z"/>
                        </svg>
                        @else
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        @endif
                    </button>
                </form>

                {{-- Bell --}}
                <button class="relative w-9 h-9 flex items-center justify-center rounded-xl transition-colors
                               text-gray-500 dark:text-gray-400
                               hover:bg-gray-100 dark:hover:bg-white/[.07]">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @php $bellCount = \App\Models\TicketPurchase::pending()->count(); @endphp
                    @if($bellCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-[17px] h-[17px] bg-red-500 text-white
                                 text-[9px] font-bold rounded-full flex items-center justify-center leading-none">
                        {{ $bellCount > 9 ? '9+' : $bellCount }}
                    </span>
                    @endif
                </button>

                {{-- Avatar / User dropdown --}}
                <div class="relative" x-data="{ open:false }">
                    <button @click="open=!open"
                            class="flex items-center gap-2 rounded-xl px-2 py-1.5 transition-colors
                                   hover:bg-gray-100 dark:hover:bg-white/[.07]">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                                     text-xs font-bold text-white uppercase shrink-0"
                             style="background:linear-gradient(135deg,#6c63ff,#4f46e5);
                                    box-shadow:0 2px 8px rgba(108,99,255,.4)">
                            {{ substr(auth()->user()->name,0,1) }}
                        </div>
                        <div class="hidden md:block text-left leading-none">
                            <p class="text-xs font-bold text-gray-800 dark:text-gray-100">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-[10px] text-gray-400 capitalize mt-0.5">
                                {{ auth()->user()->getRoleNames()->first() ?? 'Admin' }}
                            </p>
                        </div>
                        <svg class="hidden md:block w-3 h-3 text-gray-400 dark:text-gray-500 ml-0.5"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open=false" x-cloak
                         class="absolute right-0 mt-2 w-44 bg-white dark:bg-[#1e1e3a] shadow-xl
                                rounded-2xl border border-gray-100 dark:border-white/[.08]
                                z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-white/[.06]">
                            <p class="text-xs font-bold text-gray-800 dark:text-gray-100 leading-tight">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-[11px] text-gray-400 truncate mt-0.5">
                                {{ auth()->user()->email ?? auth()->user()->phone }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-3 text-red-500 dark:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center
                                           gap-2 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                {{ __('admin.logout') }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </header>

    {{-- ── PAGE CONTENT ───────────────────────────── --}}
    <main class="flex-1 p-4 sm:p-5 md:p-6" style="isolation:isolate;position:relative;z-index:0;">

        @if(session('success'))
        <div x-data="{ show:true }" x-show="show" x-cloak
             x-init="setTimeout(()=>show=false,4500)"
             x-transition:leave="transition duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm shadow-sm
                    bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700/50
                    text-green-700 dark:text-green-300">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="flex-1">{{ session('success') }}</span>
            <button @click="show=false" class="text-green-400 hover:text-green-600 leading-none text-lg">✕</button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show:true }" x-show="show" x-cloak
             x-init="setTimeout(()=>show=false,6000)"
             class="mb-4 flex items-center gap-3 rounded-2xl px-4 py-3 text-sm shadow-sm
                    bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/50
                    text-red-700 dark:text-red-300">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span class="flex-1">{{ session('error') }}</span>
            <button @click="show=false" class="text-red-400 hover:text-red-600 leading-none text-lg">✕</button>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-xs text-gray-400 dark:text-gray-600 py-4
                   border-t border-gray-200 dark:border-white/[.05]
                   bg-white dark:bg-[#16162e]">
        © {{ date('Y') }} Lottery Platform Admin. All rights reserved.
    </footer>
</div>

@stack('scripts')
</body>
</html>
