@php $isEdit = !is_null($lottery); @endphp

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
    <input type="text" name="name" value="{{ old('name', $lottery?->name) }}" required
           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                  text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                  @error('name') border-red-500 @enderror">
    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
    <textarea name="description" rows="3"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                     text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $lottery?->description) }}</textarea>
    @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ticket Price *</label>
        <input type="number" name="ticket_price" value="{{ old('ticket_price', $lottery?->ticket_price) }}"
               step="0.01" min="0.01" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                      text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                      @error('ticket_price') border-red-500 @enderror">
        @error('ticket_price')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Draw Date *</label>
        <input type="datetime-local" name="draw_date"
               value="{{ old('draw_date', $lottery?->draw_date?->format('Y-m-d\TH:i')) }}" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                      text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                      @error('draw_date') border-red-500 @enderror">
        @error('draw_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number Prefix *</label>
        <input type="text" name="number_prefix" value="{{ old('number_prefix', $lottery?->number_prefix ?? 'LOT') }}"
               maxlength="20" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                      text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('number_prefix')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Number Length *</label>
        <input type="number" name="number_length"
               value="{{ old('number_length', $lottery?->number_length ?? 6) }}" min="4" max="12" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                      text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('number_length')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max Tickets</label>
        <input type="number" name="max_tickets" value="{{ old('max_tickets', $lottery?->max_tickets) }}" min="1"
               placeholder="Unlimited"
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                      text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @error('max_tickets')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
        <select name="status"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                       text-gray-900 dark:text-gray-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @foreach(['active','inactive'] + ($isEdit ? ['completed','cancelled'] : []) as $s)
            <option value="{{ $s }}" @selected(old('status', $lottery?->status) === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
</div>
