@extends('layouts.admin')
@section('title', 'Audit Log #' . $auditLog->id)

@section('content')
<div class="max-w-3xl space-y-5">
    <a href="{{ route('admin.audit-logs.index') }}"
       class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
        ← {{ __('admin.back') }}
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Log #{{ $auditLog->id }}</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-4">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Action</dt>
                <dd class="font-mono font-medium text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $auditLog->action }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Admin</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $auditLog->admin?->name ?? 'System' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">Entity</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">
                    {{ class_basename($auditLog->entity_type) }} #{{ $auditLog->entity_id }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500 dark:text-gray-400">IP Address</dt>
                <dd class="font-mono text-gray-900 dark:text-gray-100 mt-0.5">{{ $auditLog->ip_address }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-gray-500 dark:text-gray-400">Timestamp</dt>
                <dd class="font-medium text-gray-900 dark:text-gray-100 mt-0.5">{{ $auditLog->created_at->format('M d, Y H:i:s') }}</dd>
            </div>
        </dl>

        @if($auditLog->old_values)
        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Previous Values</h3>
            <pre class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif

        @if($auditLog->new_values)
        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Values</h3>
            <pre class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 text-xs text-gray-700 dark:text-gray-300 overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>
</div>
@endsection
