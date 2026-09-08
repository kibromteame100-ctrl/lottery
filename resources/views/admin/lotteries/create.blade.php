@extends('layouts.admin')
@section('title', 'Create Lottery')

@section('content')
<div class="max-w-2xl space-y-5">
    <a href="{{ route('admin.lotteries.index') }}"
       class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
        ← {{ __('admin.back') }}
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Lottery</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.lotteries.store') }}" class="space-y-5">
            @csrf
            @include('admin.lotteries._form', ['lottery' => null])
            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    {{ __('admin.save') }}
                </button>
                <a href="{{ route('admin.lotteries.index') }}"
                   class="text-gray-600 dark:text-gray-300 px-4 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    {{ __('admin.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
