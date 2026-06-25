<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWarehouseRequest extends FormRequest
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
        $warehouse = $this->route('warehouse');
        $id = is_object($warehouse) ? $warehouse->id : $warehouse;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('warehouses', 'code')->ignore($id)],
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
