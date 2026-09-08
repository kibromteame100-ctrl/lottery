<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLotteryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['super-admin']);
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:150'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'ticket_price'  => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'draw_date'     => ['required', 'date'],
            'status'        => ['required', 'in:active,inactive,completed,cancelled'],
            'number_prefix' => ['required', 'string', 'max:20'],
            'number_length' => ['required', 'integer', 'min:4', 'max:12'],
            'max_tickets'   => ['nullable', 'integer', 'min:1'],
        ];
    }
}
