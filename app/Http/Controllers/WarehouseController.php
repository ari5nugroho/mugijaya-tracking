<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Warehouse::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('manager', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Paginate by 6 items per page to perfectly fit a 3-column grid
        $warehouses = $query->orderBy('name', 'asc')->paginate(6)->withQueryString();

        return view('warehouse.index', compact('warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWarehouseRequest $request)
    {
        $data = $request->validated();
        $data['capacity_used'] = 0;
        $data['status'] = true;

        $warehouse = Warehouse::create($data);

        return redirect()->route('warehouse.index')
            ->with('success', 'Gudang baru berhasil ditambahkan!')
            ->with('log_action', [
                'category' => 'Warehouse Management',
                'action' => 'Create Warehouse',
                'details' => "Membuat gudang baru: {$warehouse->name} ({$warehouse->code})"
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $data = $request->validated();
        
        $warehouse->update($data);

        return redirect()->route('warehouse.index')
            ->with('success', 'Informasi gudang berhasil diperbarui!')
            ->with('log_action', [
                'category' => 'Warehouse Management',
                'action' => 'Update Warehouse',
                'details' => "Mengubah data gudang ID #{$warehouse->id}: {$warehouse->name}"
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $name = $warehouse->name;
        $warehouse->delete();

        return redirect()->route('warehouse.index')
            ->with('success', 'Gudang berhasil dihapus!')
            ->with('log_action', [
                'category' => 'Warehouse Management',
                'action' => 'Delete Warehouse',
                'details' => "Menghapus gudang: {$name}"
            ]);
    }

    /**
     * Display QC Validation view.
     */
    public function validation()
    {
        return view('warehouse.validation');
    }

    /**
     * Display Loading view.
     */
    public function loading()
    {
        return view('warehouse.loading');
    }
}
