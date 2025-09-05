<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class StoreSaleItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $productId = $this->input('product_id');
            $priceInput = $this->input('price');

            // Only validate price match when a price is provided by the client
            if ($productId && $priceInput !== null) {
                $product = Product::find($productId);
                if ($product) {
                    $provided = round((float) $priceInput, 2);
                    $expected = round((float) $product->price, 2);
                    if ($provided !== $expected) {
                        $v->errors()->add('price', 'Provided price does not match product price.');
                    }
                }
            }
        });
    }
}
