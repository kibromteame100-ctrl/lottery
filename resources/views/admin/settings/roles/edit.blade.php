@extends('layouts.admin')
@section('title', 'Edit Role — ' . ucwords(str_replace('-', ' ', $role->name)))

@section('content')
<div class="max-w-3xl space-y-5">

    <a href="{{ route('admin.roles.index') }}"
       class="text-sm text-[#6c63ff] hover:underline flex items-center gap-1 w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Roles
    </a>

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                Edit Role: <span class="text-[#6c63ff]">{{ ucwords(str_replace('-', ' ', $role->name)) }}</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ $role->users()->count() }} user(s) currently assigned this role
            </p>
        </div>

        @if(!in_array($role->name, ['super-admin','payment-reviewer','report-viewer']))
        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
              onsubmit="return confirm('Delete this role?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="text-sm font-semibold text-red-500 hover:text-red-700 px-3 py-1.5
                           rounded-xl border border-red-200 dark:border-red-700/40
                           hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                Delete Role
            </button>
        </form>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-5">
        @csrf @method('PUT')
        @include('admin.settings.roles._form')
        <div class="flex gap-3">
            <button type="submit"
                    class="bg-[#6c63ff] hover:bg-[#574fd6] text-white font-bold px-6 py-2.5
                           rounded-xl transition-colors text-sm shadow-md">
                Save Changes
            </button>
            <a href="{{ route('admin.roles.index') }}"
               class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 rounded-xl
                      hover:bg-gray-100 dark:hover:bg-white/[.06] transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
