@extends('layouts.admin')
@section('title', 'Create Role')

@section('content')
<div class="max-w-3xl space-y-5">

    <a href="{{ route('admin.roles.index') }}"
       class="text-sm text-[#6c63ff] hover:underline flex items-center gap-1 w-fit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Roles
    </a>

    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Create New Role</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            Define a role name and assign the permissions it should have
        </p>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-5">
        @csrf
        @include('admin.settings.roles._form')
        <div class="flex gap-3">
            <button type="submit"
                    class="bg-[#6c63ff] hover:bg-[#574fd6] text-white font-bold px-6 py-2.5
                           rounded-xl transition-colors text-sm shadow-md">
                Create Role
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
