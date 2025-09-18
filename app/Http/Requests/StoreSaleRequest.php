<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by middleware/policies if needed
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            // cashier_id is derived from the authenticated user
        ];
    }
}
