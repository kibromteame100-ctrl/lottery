@php $isEdit = isset($expense); @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

    {{-- Title --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Title <span class="text-red-500">*</span>
        </label>
        <input type="text" name="title"
               value="{{ old('title', $expense->title ?? '') }}"
               placeholder="e.g. Office Rent – September 2026"
               class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                      px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                      @error('title') border-red-500 @enderror">
        @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Amount --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Amount (ETB) <span class="text-red-500">*</span>
        </label>
        <input type="number" name="amount" step="0.01" min="0.01"
               value="{{ old('amount', $expense->amount ?? '') }}"
               placeholder="0.00"
               class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                      px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                      @error('amount') border-red-500 @enderror">
        @error('amount')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Category --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Category <span class="text-red-500">*</span>
        </label>
        <select name="category"
                class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                       bg-white dark:bg-[#1a1a30] text-gray-800 dark:text-gray-100
                       px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                       @error('category') border-red-500 @enderror">
            @foreach($categories as $key => $label)
            <option value="{{ $key }}"
                    @selected(old('category', $expense->category ?? 'general') === $key)>
                {{ $label }}
            </option>
            @endforeach
        </select>
        @error('category')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Date --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Expense Date <span class="text-red-500">*</span>
        </label>
        <input type="date" name="expense_date"
               value="{{ old('expense_date', isset($expense) ? $expense->expense_date->toDateString() : today()->toDateString()) }}"
               max="{{ today()->toDateString() }}"
               class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                      px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                      @error('expense_date') border-red-500 @enderror">
        @error('expense_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Receipt --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Receipt (JPG/PNG/PDF, max 5MB)
        </label>
        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
               class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                      bg-white dark:bg-white/[.06] text-gray-700 dark:text-gray-300
                      px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]">
        @if($isEdit && $expense->receipt_path)
        <p class="mt-1 text-xs text-emerald-500">
            ✓ Receipt already uploaded —
            <a href="{{ route('admin.expenses.receipt', $expense) }}" target="_blank"
               class="underline">view</a>
        </p>
        @endif
        @error('receipt')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Description --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Description
        </label>
        <textarea name="description" rows="3"
                  placeholder="Optional details about this expense…"
                  class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                         bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                         px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                         resize-none">{{ old('description', $expense->description ?? '') }}</textarea>
        @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    {{-- Notes --}}
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
            Notes / Justification
        </label>
        <textarea name="notes" rows="2"
                  placeholder="Internal notes (optional)…"
                  class="w-full rounded-xl border border-gray-200 dark:border-white/[.1]
                         bg-white dark:bg-white/[.06] text-gray-800 dark:text-gray-100
                         px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#6c63ff]
                         resize-none">{{ old('notes', $expense->notes ?? '') }}</textarea>
        @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
</div>
