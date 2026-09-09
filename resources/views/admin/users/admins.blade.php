@extends('layouts.admin')
@section('title', __('admin.admin_users'))

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('admin.admin_users') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ __('admin.manage_admins_sub') }}
            </p>
        </div>
        @can('manageAdmins', \App\Models\User::class)
        <a href="{{ route('admin.users.create-admin') }}"
           class="inline-flex items-center gap-2 bg-[#6c63ff] hover:bg-[#574fd6] text-white
                  font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('admin.new_admin') }}
        </a>
        @endcan
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.users.admins') }}"
          class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-4">
        <div class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('admin.search') }}…"
                   class="flex-1 min-w-[180px] rounded-xl border border-gray-200 dark:border-white/[.1]
                          bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                          placeholder-gray-400 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
            <select name="role"
                    class="rounded-xl border border-gray-200 dark:border-white/[.1]
                           bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                           px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
                <option value="">{{ __('admin.all_roles') }}</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" @selected(request('role')===$role->name)>
                    {{ ucwords(str_replace('-', ' ', $role->name)) }}
                </option>
                @endforeach
            </select>
            <button type="submit"
                    class="bg-[#6c63ff] hover:bg-[#574fd6] text-white px-4 py-2 rounded-xl text-sm font-semibold">
                {{ __('admin.filter') }}
            </button>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[540px]">
                <thead class="bg-gray-50 dark:bg-white/[.04]">
                    <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-5 py-3">{{ __('admin.admin') }}</th>
                        <th class="px-5 py-3">{{ __('admin.role') }}</th>
                        <th class="px-5 py-3">{{ __('admin.status') }}</th>
                        <th class="px-5 py-3">{{ __('admin.joined') }}</th>
                        <th class="px-5 py-3">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[.04]">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/[.03] transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full text-white text-sm font-bold uppercase
                                             flex items-center justify-center shrink-0
                                             {{ $admin->id === auth()->id() ? 'ring-2 ring-[#6c63ff]' : '' }}"
                                     style="background:linear-gradient(135deg,#6c63ff,#4f46e5)">
                                    {{ substr($admin->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                                        {{ $admin->name }}
                                        @if($admin->id === auth()->id())
                                        <span class="ml-1 text-[10px] bg-[#6c63ff]/10 text-[#6c63ff]
                                                      dark:text-[#8b85ff] px-1.5 py-0.5 rounded-lg font-bold">
                                            {{ __('admin.you') }}
                                        </span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $admin->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            @foreach($admin->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                          bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                {{ ucwords(str_replace('-', ' ', $role->name)) }}
                            </span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3">
                            @include('components.status-badge', ['status' => $admin->status])
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 dark:text-gray-400">
                            {{ $admin->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                @can('manageAdmins', \App\Models\User::class)
                                <a href="{{ route('admin.users.edit-admin', $admin) }}"
                                   class="text-indigo-500 hover:underline text-xs font-semibold">{{ __('admin.edit') }}</a>

                                @if($admin->id !== auth()->id())
                                <form method="POST"
                                      action="{{ route('admin.users.destroy-admin', $admin) }}"
                                      onsubmit="return confirm('{{ __('admin.confirm_delete_admin') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-500 hover:underline text-xs font-semibold">
                                        {{ __('admin.delete') }}
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                            <div class="text-4xl mb-2 opacity-30">👤</div>
                            {{ __('admin.no_admins') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($admins->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-white/[.05]">
            {{ $admins->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
