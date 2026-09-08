@extends('layouts.admin')
@section('title', 'Permissions')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Permissions</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">All available permissions in the system</p>
    </div>

    {{-- Settings tabs --}}
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

    {{-- Sub-tabs --}}
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div class="flex gap-2">
            <a href="{{ route('admin.roles.index') }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors
                      bg-gray-100 dark:bg-white/[.07] text-gray-600 dark:text-gray-300
                      hover:bg-gray-200 dark:hover:bg-white/[.12]">
                🛡️ Roles
            </a>
            <a href="{{ route('admin.roles.permissions') }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold bg-[#6c63ff] text-white shadow">
                🔑 Permissions
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Add new permission --}}
        <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-5 xl:col-span-1 h-fit">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-4">
                Add New Permission
            </h2>
            <form method="POST" action="{{ route('admin.roles.permissions.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Permission Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. view reports"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                                  bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                                  px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                                  @error('name') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-400">
                        Convention: <code>action resource</code> — e.g. <code>view reports</code>, <code>delete lotteries</code>
                    </p>
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        class="w-full bg-[#6c63ff] hover:bg-[#574fd6] text-white font-bold
                               py-2.5 rounded-xl text-sm transition-colors">
                    Add Permission
                </button>
            </form>
        </div>

        {{-- Permissions list --}}
        <div class="xl:col-span-2 bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/[.05]">
                <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm">
                    All Permissions
                    <span class="ml-2 text-xs font-normal text-gray-400">({{ $permissions->count() }} total)</span>
                </h2>
            </div>

            @php
            $grouped = $permissions->groupBy(function($p) {
                return explode(' ', $p->name, 2)[1] ?? 'general';
            });
            @endphp

            <div class="divide-y divide-gray-100 dark:divide-white/[.04]">
                @foreach($grouped as $resource => $perms)
                <div class="px-5 py-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">
                        {{ ucfirst($resource) }}
                    </p>
                    <div class="space-y-2">
                        @foreach($perms as $perm)
                        <div class="flex items-center justify-between gap-3 p-2.5 rounded-xl
                                     bg-gray-50 dark:bg-white/[.03] hover:bg-gray-100 dark:hover:bg-white/[.06]
                                     transition-colors group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-6 h-6 rounded-lg bg-[#6c63ff]/10 flex items-center
                                             justify-center shrink-0">
                                    <svg class="w-3 h-3 text-[#6c63ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-100 font-mono block truncate">
                                        {{ $perm->name }}
                                    </span>
                                    <span class="text-[10px] text-gray-400">
                                        Used by {{ $perm->roles_count }} {{ $perm->roles_count === 1 ? 'role' : 'roles' }}
                                    </span>
                                </div>
                            </div>

                            @if($perm->roles_count === 0)
                            <form method="POST"
                                  action="{{ route('admin.roles.permissions.destroy', $perm) }}"
                                  onsubmit="return confirm('Delete permission \'{{ $perm->name }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="opacity-0 group-hover:opacity-100 w-6 h-6 flex items-center
                                               justify-center rounded-lg text-red-500
                                               hover:bg-red-50 dark:hover:bg-red-900/30 transition-all"
                                        title="Delete permission">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                            @else
                            <span class="text-[10px] text-gray-400 shrink-0">in use</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
