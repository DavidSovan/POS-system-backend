<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StockAdjustmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('accessInventory', auth()->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['sometimes', 'required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => [
                'required',
                'string',
                Rule::in([
                    'purchase', 'sale', 'adjustment', 'damaged', 'lost', 
                    'returned', 'expired', 'transfer_in', 'transfer_out',
                    'stock_take', 'correction'
                ])
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'reference' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'type.required' => 'Movement type is required.',
            'type.in' => 'Movement type must be "in" or "out".',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'reason.required' => 'Reason for stock movement is required.',
            'reason.in' => 'Invalid reason provided.',
            'unit_cost.numeric' => 'Unit cost must be a valid number.',
            'unit_cost.min' => 'Unit cost cannot be negative.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'reference.max' => 'Reference cannot exceed 100 characters.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // For 'in' movements, unit_cost should be provided for better tracking
            if ($this->type === 'in' && $this->reason === 'purchase' && !$this->unit_cost) {
                $validator->errors()->add(
                    'unit_cost', 
                    'Unit cost is recommended for purchase transactions.'
                );
            }
        });
    }
}
