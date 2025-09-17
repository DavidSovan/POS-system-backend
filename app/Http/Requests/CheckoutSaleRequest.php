<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:cash,card'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
