<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product');
        
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sku' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'decimal:0,2'],
            'cost' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'reorder_level' => ['sometimes', 'required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($productId)],
            'status' => ['sometimes', 'required', 'in:active,inactive,discontinued'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'sku.required' => 'Product SKU is required.',
            'sku.unique' => 'This SKU is already in use.',
            'category_id.required' => 'Product category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'price.required' => 'Product price is required.',
            'price.min' => 'Product price must be at least 0.',
            'cost.min' => 'Product cost must be at least 0.',
            'reorder_level.required' => 'Reorder level is required.',
            'reorder_level.min' => 'Reorder level cannot be negative.',
            'barcode.unique' => 'This barcode is already in use.',
            'status.in' => 'Status must be active, inactive, or discontinued.',
        ];
    }
}
