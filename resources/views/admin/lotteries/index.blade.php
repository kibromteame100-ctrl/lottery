@extends('layouts.admin')
@section('title', __('admin.lotteries'))

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.lotteries') }}</h1>
        @can('create', \App\Models\Lottery::class)
        <a href="{{ route('admin.lotteries.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
            + Create Lottery
        </a>
        @endcan
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b dark:border-gray-700">
                    <tr class="text-left text-gray-600 dark:text-gray-400">
                        <th class="px-4 py-3 font-medium">{{ __('admin.name') }}</th>
                        <th class="px-4 py-3 font-medium">Price</th>
                        <th class="px-4 py-3 font-medium">Draw Date</th>
                        <th class="px-4 py-3 font-medium">Tickets Sold</th>
                        <th class="px-4 py-3 font-medium">Approved</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($lotteries as $lottery)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">
                            {{ $lottery->name }}
                            <div class="text-xs text-gray-400 font-normal">{{ $lottery->number_prefix }}-YYYY-XXXXXX</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                            {{ number_format($lottery->ticket_price, 2) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $lottery->draw_date->format('M d, Y') }}
                            <div class="text-xs text-gray-400">{{ $lottery->draw_date->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-200">
                            {{ number_format($lottery->ticket_purchases_count) }}
                            @if($lottery->max_tickets)
                            <span class="text-xs text-gray-400">/ {{ $lottery->max_tickets }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-green-600 dark:text-green-400 font-medium">
                            {{ number_format($lottery->approved_count) }}
                        </td>
                        <td class="px-4 py-3">
                            @include('components.status-badge', ['status' => $lottery->status])
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @can('update', $lottery)
                                <a href="{{ route('admin.lotteries.edit', $lottery) }}"
                                   class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium">
                                    {{ __('admin.edit') }}
                                </a>
                                @endcan
                                @can('delete', $lottery)
                                <form method="POST" action="{{ route('admin.lotteries.destroy', $lottery) }}"
                                      onsubmit="return confirm('Delete this lottery?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-500 hover:text-red-700 text-xs font-medium">
                                        {{ __('admin.delete') }}
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                            {{ __('admin.no_records') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lotteries->hasPages())
        <div class="px-4 py-3 border-t dark:border-gray-700">
            {{ $lotteries->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
