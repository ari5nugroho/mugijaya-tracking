<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:50', 'unique:warehouses,code'],
            'name' => ['required', 'string', 'max:255'],
            'manager' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'address' => ['required', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'code' => 'Kode Gudang',
            'name' => 'Nama Gudang',
            'manager' => 'Kepala Gudang',
            'capacity' => 'Kapasitas',
            'address' => 'Alamat',
        ];
    }
}
