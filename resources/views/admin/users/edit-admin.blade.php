@extends('layouts.admin')
@section('title', 'Edit Admin — ' . $user->name)

@section('content')
<div class="max-w-2xl space-y-5">
    <a href="{{ route('admin.users.admins') }}"
       class="text-sm text-[#6c63ff] hover:underline flex items-center gap-1 w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Admin Users
    </a>

    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Edit Admin</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $user->name }} — {{ $user->email }}</p>
    </div>

    <div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-6">
        <form method="POST" action="{{ route('admin.users.update-admin', $user) }}" class="space-y-5">
            @csrf @method('PUT')
            @include('admin.users._admin_form')
            <div class="flex gap-3 pt-3">
                <button type="submit"
                        class="bg-[#6c63ff] hover:bg-[#574fd6] text-white font-bold px-6 py-2.5
                               rounded-xl transition-colors text-sm">
                    Save Changes
                </button>
                <a href="{{ route('admin.users.admins') }}"
                   class="px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 rounded-xl
                          hover:bg-gray-100 dark:hover:bg-white/[.06] transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
