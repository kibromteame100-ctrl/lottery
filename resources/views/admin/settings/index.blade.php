@extends('layouts.admin')
@section('title', __('admin.settings'))

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ __('admin.settings') }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">System configuration and access control</p>
    </div>

    {{-- Settings tabs --}}
    <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-white/[.07] pb-0">
        <a href="{{ route('admin.settings.index') }}"
           class="px-4 py-2.5 text-sm font-semibold rounded-t-xl border-b-2 transition-colors
                  {{ request()->routeIs('admin.settings.index')
                     ? 'border-[#6c63ff] text-[#6c63ff] dark:text-[#8b85ff] bg-white dark:bg-[#1a1a30]'
                     : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
            ⚙️ General
        </a>
        <a href="{{ route('admin.roles.index') }}"
           class="px-4 py-2.5 text-sm font-semibold rounded-t-xl border-b-2 transition-colors
                  {{ request()->routeIs('admin.roles.*')
                     ? 'border-[#6c63ff] text-[#6c63ff] dark:text-[#8b85ff] bg-white dark:bg-[#1a1a30]'
                     : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
            🛡️ Roles & Permissions
        </a>
    </div>

    {{-- General Settings Form --}}
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
        @csrf @method('PUT')

        @forelse($grouped as $group => $items)
        <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-6">
            <h2 class="font-bold text-gray-700 dark:text-gray-200 text-sm mb-4 capitalize flex items-center gap-2">
                @php
                $groupIcons = ['general'=>'⚙️','upload'=>'📎','payment'=>'💳','system'=>'🖥️'];
                @endphp
                <span>{{ $groupIcons[$group] ?? '⚙️' }}</span>
                <span>{{ ucfirst($group) }}</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($items as $setting)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        {{ $setting['label'] ?? $setting['key'] }}
                    </label>
                    @if($setting['type'] === 'boolean')
                    <select name="settings[{{ $setting['key'] }}]"
                            class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                                   bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                                   px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
                        <option value="1" @selected($setting['value'] == '1')>Yes</option>
                        <option value="0" @selected($setting['value'] == '0')>No</option>
                    </select>
                    @else
                    <input type="text" name="settings[{{ $setting['key'] }}]"
                           value="{{ old('settings.' . $setting['key'], $setting['value']) }}"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                                  bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                                  px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
                    @endif
                    <p class="mt-0.5 text-[10px] text-gray-400 font-mono">{{ $setting['key'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-8 text-center text-gray-400 text-sm">
            <div class="text-4xl mb-2 opacity-30">⚙️</div>
            No settings configured yet. Run the settings seeder to populate defaults.
        </div>
        @endforelse

        @if($grouped->count())
        <div>
            <button type="submit"
                    class="bg-[#6c63ff] hover:bg-[#574fd6] text-white font-bold
                           px-6 py-2.5 rounded-xl transition-colors text-sm shadow-md">
                Save Settings
            </button>
        </div>
        @endif
    </form>
</div>
@endsection
