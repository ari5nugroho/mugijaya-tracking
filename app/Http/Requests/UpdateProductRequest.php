<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $id = is_object($product) ? $product->id : $product;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($id)],
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'Kategori',
            'sku' => 'SKU',
            'name' => 'Nama Produk',
            'weight' => 'Berat',
            'length' => 'Panjang',
            'width' => 'Lebar',
            'height' => 'Tinggi',
            'description' => 'Deskripsi',
            'status' => 'Status',
            'price' => 'Harga Dasar',
            'unit' => 'Satuan Unit',
        ];
    }
}
