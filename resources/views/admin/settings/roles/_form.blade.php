@php
    $isEdit      = isset($role) && $role !== null;
    $role        = $role ?? null;
    $assignedIds = $role?->permissions->pluck('id')->toArray() ?? [];
@endphp

{{-- Role name --}}
<div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-5">
    <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-4">Role Details</h2>
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Role Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name"
               value="{{ old('name', $role->name ?? '') }}"
               placeholder="e.g. lottery-manager"
               pattern="[a-z0-9\-]+"
               title="Only lowercase letters, numbers, and hyphens"
               {{ $isEdit && in_array($role?->name, ['super-admin','payment-reviewer','report-viewer']) ? 'readonly' : '' }}
               class="w-full sm:w-80 rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                      px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                      @error('name') border-red-500 @enderror
                      {{ $isEdit && in_array($role?->name ?? '', ['super-admin','payment-reviewer','report-viewer']) ? 'opacity-60 cursor-not-allowed' : '' }}">
        <p class="mt-1 text-xs text-gray-400">Lowercase letters, numbers, hyphens only. Example: <code>lottery-manager</code></p>
        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Permission matrix --}}
<div class="bg-white dark:bg-[#1a1a30] rounded-2xl shadow-card p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-bold text-gray-800 dark:text-gray-100 text-sm">Assign Permissions</h2>
        <div class="flex gap-2">
            <button type="button" id="selectAll"
                    class="text-xs font-semibold text-[#6c63ff] hover:underline">Select all</button>
            <span class="text-gray-300 dark:text-gray-600">|</span>
            <button type="button" id="clearAll"
                    class="text-xs font-semibold text-gray-500 hover:underline">Clear all</button>
        </div>
    </div>

    <div class="space-y-5">
        @foreach($permissions as $resource => $perms)
        <div>
            {{-- Resource group header --}}
            <div class="flex items-center gap-2 mb-2">
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    {{ ucfirst($resource) }}
                </span>
                <div class="flex-1 h-px bg-gray-100 dark:bg-white/[.06]"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                @foreach($perms as $perm)
                <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer
                               border border-gray-100 dark:border-white/[.06]
                               hover:bg-gray-50 dark:hover:bg-white/[.04]
                               has-[:checked]:border-[#6c63ff]/50
                               has-[:checked]:bg-[#6c63ff]/5
                               dark:has-[:checked]:bg-[#6c63ff]/10
                               transition-colors group">
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $perm->id }}"
                           {{ in_array($perm->id, old('permissions', $assignedIds)) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-[#6c63ff] border-gray-300 dark:border-gray-600
                                  focus:ring-[#6c63ff] focus:ring-offset-0 perm-checkbox cursor-pointer">
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-tight
                                  group-has-[:checked]:text-[#6c63ff] dark:group-has-[:checked]:text-[#8b85ff]">
                        {{ $perm->name }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @error('permissions')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('click', () => {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
});
document.getElementById('clearAll').addEventListener('click', () => {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
});
</script>
@endpush
