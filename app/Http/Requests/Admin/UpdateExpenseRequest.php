<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('expense'));
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:200'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'amount'       => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'category'     => ['required', 'string', 'in:' . implode(',', array_keys(\App\Models\Expense::categories()))],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'notes'        => ['nullable', 'string', 'max:500'],
            'receipt'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
