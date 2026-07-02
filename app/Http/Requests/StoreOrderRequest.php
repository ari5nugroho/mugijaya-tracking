<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'driver_id' => ['nullable', 'exists:users,id'],
            'order_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', Rule::in(['raw_material', 'finished_product'])],

            // Wajib jika item_type = raw_material
            'items.*.product_id' => ['required_if:items.*.item_type,raw_material', 'nullable', 'exists:products,id'],
            'items.*.quantity_ordered' => ['required_if:items.*.item_type,raw_material', 'nullable', 'integer', 'min:1'],

            // Wajib jika item_type = finished_product
            'items.*.finished_product_id' => ['required_if:items.*.item_type,finished_product', 'nullable', 'exists:finished_products,id'],
            'items.*.custom_width' => ['required_if:items.*.item_type,finished_product', 'nullable', 'numeric', 'min:0.01'],
            'items.*.custom_height' => ['required_if:items.*.item_type,finished_product', 'nullable', 'numeric', 'min:0.01'],

            'items.*.price_per_unit' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'warehouse_id' => 'Gudang Asal',
            'customer_name' => 'Nama Pelanggan',
            'customer_address' => 'Alamat Pelanggan',
            'customer_phone' => 'Nomor Telepon',
            'driver_id' => 'Driver',
            'order_date' => 'Tanggal Order',
            'delivery_date' => 'Tanggal Pengiriman',
            'items' => 'Daftar Item',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Order harus memiliki minimal satu item.',
            'items.*.product_id.required_if' => 'Bahan baku wajib dipilih untuk item jenis bahan baku.',
            'items.*.quantity_ordered.required_if' => 'Jumlah pesanan wajib diisi untuk item jenis bahan baku.',
            'items.*.finished_product_id.required_if' => 'Jenis fabrikasi wajib dipilih untuk item custom.',
            'items.*.custom_width.required_if' => 'Lebar wajib diisi untuk item fabrikasi custom.',
            'items.*.custom_height.required_if' => 'Tinggi wajib diisi untuk item fabrikasi custom.',
        ];
    }
}
