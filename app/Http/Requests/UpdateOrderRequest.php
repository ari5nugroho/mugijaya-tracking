<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
     *
     * Catatan: status TIDAK divalidasi di sini. Perubahan status order
     * (pending -> processing -> checklist_mandor -> ... -> delivered/cancelled)
     * ditangani lewat method khusus di OrderController (mis. assignDriver,
     * updateStatus), bukan lewat form update biasa, supaya transisi status
     * tetap terkontrol dan tidak bisa "dilompati" sembarangan dari form edit.
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
            'items.*.id' => ['nullable', 'integer', 'exists:order_items,id'],
            'items.*.item_type' => ['required', Rule::in(['raw_material', 'finished_product'])],
            'items.*.product_id' => ['required_if:items.*.item_type,raw_material', 'nullable', 'exists:products,id'],
            'items.*.quantity_ordered' => ['required_if:items.*.item_type,raw_material', 'nullable', 'integer', 'min:1'],
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
}
