<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TicketPurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $maxSizeMb = (int) config('lottery.max_screenshot_size_mb', 5);

        return [
            'lottery_id'     => ['required', 'integer', 'exists:lotteries,id'],
            'quantity'       => ['sometimes', 'integer', 'min:1', 'max:100'],
            'transaction_id' => ['required', 'string', 'max:100', 'unique:ticket_purchases,transaction_id'],
            'payment_method' => ['required', 'string', 'max:50'],
            'screenshot'     => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:' . ($maxSizeMb * 1024),
            ],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => __('validation.failed'),
            'errors'  => $validator->errors(),
        ], 422));
    }
}
