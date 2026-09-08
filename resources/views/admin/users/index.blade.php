@extends('layouts.admin')
@section('title', __('admin.users'))

@section('content')
<div class="space-y-5">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.users') }}</h1>

    <form method="GET" action="{{ route('admin.users.index') }}"
          class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('admin.search') }} (name, phone, email)"
                   class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                          text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <select name="status"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                @foreach(['active','inactive','suspended'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                {{ __('admin.filter') }}
            </button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.users.index') }}"
               class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 px-3 py-2">Clear</a>
            @endif
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th class="px-4 py-3 font-medium">{{ __('admin.name') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.phone') }} / {{ __('admin.email') }}</th>
                        <th class="px-4 py-3 font-medium">Tickets</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 font-medium">Joined</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $user->name }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                            {{ $user->phone ?? $user->email ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                            {{ $user->ticket_purchases_count }}
                        </td>
                        <td class="px-4 py-3">
                            @include('components.status-badge', ['status' => $user->status])
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium">
                                    {{ __('admin.view') }}
                                </a>
                                @role('super-admin')
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-medium {{ $user->status === 'active' ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }}">
                                        {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Are you sure you want to permanently delete {{ addslashes($user->name) }}? This action cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        Delete
                                    </button>
                                </form>
                                @endrole
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">{{ __('admin.no_records') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-4 py-3 border-t dark:border-gray-700">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
