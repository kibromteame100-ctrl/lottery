@extends('layouts.admin')
@section('title', 'Roles & Permissions')

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Roles & Permissions</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage roles and the permissions assigned to each</p>
    </div>

    {{-- Settings tabs (reused) --}}
    <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-white/[.07]">
        <a href="{{ route('admin.settings.index') }}"
           class="px-4 py-2.5 text-sm font-semibold rounded-t-xl border-b-2 transition-colors
                  border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            ⚙️ General
        </a>
        <a href="{{ route('admin.roles.index') }}"
           class="px-4 py-2.5 text-sm font-semibold rounded-t-xl border-b-2 transition-colors
                  border-[#6c63ff] text-[#6c63ff] dark:text-[#8b85ff] bg-white dark:bg-[#1a1a30]">
            🛡️ Roles & Permissions
        </a>
    </div>

    {{-- Sub-tabs: Roles / Permissions --}}
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex gap-2">
            <a href="{{ route('admin.roles.index') }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors
                      {{ !request()->routeIs('admin.roles.permissions')
                         ? 'bg-[#6c63ff] text-white shadow'
                         : 'bg-gray-100 dark:bg-white/[.07] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/[.12]' }}">
                🛡️ Roles
            </a>
            <a href="{{ route('admin.roles.permissions') }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors
                      {{ request()->routeIs('admin.roles.permissions')
                         ? 'bg-[#6c63ff] text-white shadow'
                         : 'bg-gray-100 dark:bg-white/[.07] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/[.12]' }}">
                🔑 Permissions
            </a>
        </div>

        <a href="{{ route('admin.roles.create') }}"
           class="inline-flex items-center gap-2 bg-[#6c63ff] hover:bg-[#574fd6] text-white
                  font-semibold text-sm px-4 py-2 rounded-xl transition-colors shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Role
        </a>
    </div>

    {{-- Roles grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($roles as $role)
        @php
        $isBuiltIn = in_array($role->name, ['super-admin','payment-reviewer','report-viewer']);
        $roleColors = [
            'super-admin'       => ['bg'=>'bg-purple-100 dark:bg-purple-900/30', 'text'=>'text-purple-700 dark:text-purple-300', 'border'=>'border-purple-200 dark:border-purple-700/50'],
            'payment-reviewer'  => ['bg'=>'bg-blue-100 dark:bg-blue-900/30',   'text'=>'text-blue-700 dark:text-blue-300',   'border'=>'border-blue-200 dark:border-blue-700/50'],
            'report-viewer'     => ['bg'=>'bg-green-100 dark:bg-green-900/30', 'text'=>'text-green-700 dark:text-green-300', 'border'=>'border-green-200 dark:border-green-700/50'],
        ];
        $c = $roleColors[$role->name] ?? ['bg'=>'bg-gray-100 dark:bg-white/[.07]','text'=>'text-gray-700 dark:text-gray-300','border'=>'border-gray-200 dark:border-white/[.1]'];
        @endphp
        <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card border
                    border-gray-100 dark:border-white/[.06] overflow-hidden">

            {{-- Role header --}}
            <div class="px-5 py-4 border-b border-gray-100 dark:border-white/[.05]
                         flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center shrink-0 text-lg">
                        🛡️
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-gray-900 dark:text-white text-sm">
                                {{ ucwords(str_replace('-', ' ', $role->name)) }}
                            </h3>
                            @if($isBuiltIn)
                            <span class="text-[10px] font-bold bg-gray-100 dark:bg-white/[.08]
                                          text-gray-500 dark:text-gray-400 px-1.5 py-0.5 rounded-lg">
                                Built-in
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $role->name }}</p>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="{{ route('admin.roles.edit', $role) }}"
                       class="w-7 h-7 flex items-center justify-center rounded-lg
                              text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30
                              transition-colors" title="Edit role">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    @if(!$isBuiltIn)
                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                          onsubmit="return confirm('Delete role \'{{ $role->name }}\'?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-7 h-7 flex items-center justify-center rounded-lg
                                       text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30
                                       transition-colors" title="Delete role">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Stats bar --}}
            <div class="px-5 py-3 bg-gray-50 dark:bg-white/[.02] flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300 font-medium">
                    <svg class="w-3.5 h-3.5 text-[#6c63ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $role->users_count }} {{ $role->users_count === 1 ? 'user' : 'users' }}
                </span>
                <span class="flex items-center gap-1.5 text-gray-600 dark:text-gray-300 font-medium">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    {{ $role->permissions->count() }} {{ $role->permissions->count() === 1 ? 'permission' : 'permissions' }}
                </span>
            </div>

            {{-- Permissions list --}}
            <div class="px-5 py-3">
                @if($role->permissions->isEmpty())
                <p class="text-xs text-gray-400 italic">No permissions assigned.</p>
                @else
                <div class="flex flex-wrap gap-1.5">
                    @foreach($role->permissions->sortBy('name') as $perm)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold
                                  bg-gray-100 dark:bg-white/[.07] text-gray-600 dark:text-gray-300">
                        {{ $perm->name }}
                    </span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-12 text-center text-gray-400">
            <div class="text-4xl mb-3 opacity-30">🛡️</div>
            <p>No roles found. Create one to get started.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
