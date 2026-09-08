@php
    $classes = match($status) {
        'pending'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
        'approved'  => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        'rejected'  => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        'cancelled' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'active'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        'inactive'  => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'completed' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
        default     => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    };
    $label = __('admin.status_' . $status, [], app()->getLocale()) !== 'admin.status_' . $status
        ? __('admin.status_' . $status)
        : ucfirst($status);
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
    {{ $label }}
</span>
