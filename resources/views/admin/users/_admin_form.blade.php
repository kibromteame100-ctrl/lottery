@php
    $isEdit = isset($user) && $user !== null;
    $user   = $user ?? null;   // ensure $user is always defined in this partial
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Full Name <span class="text-red-500">*</span>
        </label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
               class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                      px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                      @error('name') border-red-500 @enderror">
        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Email Address <span class="text-red-500">*</span>
        </label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
               class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                      px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                      @error('email') border-red-500 @enderror">
        @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $isEdit ? 'New Password' : 'Password' }}
            @if(!$isEdit)<span class="text-red-500">*</span>@endif
        </label>
        @if($isEdit)
        <p class="text-xs text-gray-400 mb-1.5">Leave blank to keep current password</p>
        @endif
        <input type="password" name="password" {{ !$isEdit ? 'required' : '' }}
               autocomplete="new-password"
               class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                      px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                      @error('password') border-red-500 @enderror">
        @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Confirm Password {{ !$isEdit ? '*' : '' }}
        </label>
        <input type="password" name="password_confirmation"
               {{ !$isEdit ? 'required' : '' }}
               autocomplete="new-password"
               class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                      px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Role <span class="text-red-500">*</span>
        </label>
        <select name="role" required
                class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                       bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                       px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                       @error('role') border-red-500 @enderror">
            <option value="">Select role…</option>
            @foreach($roles as $role)
            <option value="{{ $role->name }}"
                    @selected(old('role', $user?->getRoleNames()->first() ?? '') === $role->name)>
                {{ ucwords(str_replace('-', ' ', $role->name)) }}
            </option>
            @endforeach
        </select>
        @error('role')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    @if($isEdit)
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Status <span class="text-red-500">*</span>
        </label>
        <select name="status" required
                class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                       bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                       px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
            @foreach(['active','inactive','suspended'] as $s)
            <option value="{{ $s }}" @selected(old('status', $user?->status ?? 'active') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
    @endif

</div>

{{-- Role permissions info --}}
<div class="mt-5 bg-blue-50 dark:bg-blue-900/15 border border-blue-200 dark:border-blue-700/40
            rounded-xl p-4 text-xs text-blue-700 dark:text-blue-300">
    <p class="font-bold mb-2">Role Permissions:</p>
    <ul class="space-y-1">
        <li><strong>Super Admin</strong> — Full access: all tickets, lotteries, users, expenses, reports, settings</li>
        <li><strong>Payment Reviewer</strong> — View & approve/reject tickets; view & create expenses</li>
        <li><strong>Report Viewer</strong> — Read-only access to tickets, lotteries, users, reports, expenses</li>
    </ul>
</div>
