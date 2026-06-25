<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    /**
     * Display a listing of the inventory stocks.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $warehouseId = $request->input('warehouse_id');

        $query = Stock::with(['product', 'warehouse']);

        if ($warehouseId && $warehouseId !== 'all') {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Paginate by 10 items
        $stocks = $query->orderBy('quantity', 'asc')->paginate(10)->withQueryString();

        // Fetch low stock items for restock warnings panel
        $lowStocks = Stock::with(['product', 'warehouse'])
            ->whereColumn('quantity', '<=', 'minimum_stock')
            ->get();

        // Fetch recent mutation logs
        $recentMovements = StockMovement::with(['product', 'warehouse', 'user'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // Fetch all warehouses and products for selectors
        $warehouses = Warehouse::where('status', true)->orderBy('name', 'asc')->get();
        $products = Product::where('status', true)->orderBy('name', 'asc')->get();

        return view('inventory.index', compact('stocks', 'lowStocks', 'recentMovements', 'warehouses', 'products'));
    }

    /**
     * Update the minimum stock limit.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $stock = Stock::findOrFail($id);
        $oldMinStock = $stock->minimum_stock;
        
        $stock->update([
            'minimum_stock' => $request->input('minimum_stock'),
        ]);

        activity()
            ->useLog('inventory')
            ->performedOn($stock)
            ->event('adjustment')
            ->withProperties([
                'old' => ['minimum_stock' => $oldMinStock],
                'attributes' => ['minimum_stock' => $stock->minimum_stock],
                'product' => $stock->product->name,
                'sku' => $stock->product->sku,
                'warehouse' => $stock->warehouse->name
            ])
            ->log("Batas stok minimum produk '{$stock->product->name}' ({$stock->product->sku}) di gudang '{$stock->warehouse->name}' diubah dari {$oldMinStock} menjadi {$stock->minimum_stock}");

        return redirect()->route('inventory.index')
            ->with('success', 'Batas stok minimum berhasil diperbarui!')
            ->with('log_action', [
                'category' => 'Stock Adjustment',
                'action' => 'Adjust Limit',
                'details' => "Mengubah batas stok minimum {$stock->product->sku} di {$stock->warehouse->name} menjadi {$stock->minimum_stock}"
            ]);
    }

    /**
     * Process stock mutation (In, Out, Transfer, Adjustment).
     */
    public function mutate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:IN,OUT,TRANSFER,ADJUSTMENT'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['required', 'string', 'max:255'],
            'warehouse_id' => ['required_unless:type,TRANSFER', 'nullable', 'exists:warehouses,id'],
            'source_warehouse_id' => ['required_if:type,TRANSFER', 'nullable', 'exists:warehouses,id'],
            'destination_warehouse_id' => ['required_if:type,TRANSFER', 'nullable', 'exists:warehouses,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('open_modal', 'adjustStockModal');
        }

        $productId = $request->input('product_id');
        $type = $request->input('type');
        $qty = (int) $request->input('quantity');
        $notes = $request->input('notes');
        $userId = Auth::id();

        try {
            DB::transaction(function () use ($request, $productId, $type, $qty, $notes, $userId) {
                if ($type === 'TRANSFER') {
                    $sourceWhId = $request->input('source_warehouse_id');
                    $destWhId = $request->input('destination_warehouse_id');

                    if ($sourceWhId == $destWhId) {
                        throw new \Exception('Gudang asal dan tujuan tidak boleh sama.');
                    }

                    // 1. Process Source Warehouse (OUT)
                    $sourceStock = Stock::where('warehouse_id', $sourceWhId)
                        ->where('product_id', $productId)
                        ->first();

                    if (!$sourceStock || $sourceStock->quantity < $qty) {
                        throw new \Exception('Stok di gudang asal tidak mencukupi untuk melakukan transfer.');
                    }

                    $srcQtyBefore = $sourceStock->quantity;
                    $srcQtyAfter = $srcQtyBefore - $qty;

                    $sourceStock->update(['quantity' => $srcQtyAfter]);

                    StockMovement::create([
                        'warehouse_id' => $sourceWhId,
                        'product_id' => $productId,
                        'type' => 'TRANSFER',
                        'quantity_before' => $srcQtyBefore,
                        'quantity_change' => -$qty,
                        'quantity_after' => $srcQtyAfter,
                        'notes' => "Transfer ke " . Warehouse::find($destWhId)->name . ". " . $notes,
                        'created_by' => $userId
                    ]);

                    // 2. Process Destination Warehouse (IN)
                    $destStock = Stock::firstOrCreate(
                        ['warehouse_id' => $destWhId, 'product_id' => $productId],
                        ['quantity' => 0, 'minimum_stock' => 10]
                    );

                    $destQtyBefore = $destStock->quantity;
                    $destQtyAfter = $destQtyBefore + $qty;

                    $destStock->update(['quantity' => $destQtyAfter]);

                    StockMovement::create([
                        'warehouse_id' => $destWhId,
                        'product_id' => $productId,
                        'type' => 'TRANSFER',
                        'quantity_before' => $destQtyBefore,
                        'quantity_change' => $qty,
                        'quantity_after' => $destQtyAfter,
                        'notes' => "Transfer dari " . Warehouse::find($sourceWhId)->name . ". " . $notes,
                        'created_by' => $userId
                    ]);

                    // Log activity
                    $sourceWhName = Warehouse::find($sourceWhId)->name;
                    $destWhName = Warehouse::find($destWhId)->name;
                    $product = Product::find($productId);
                    
                    activity()
                        ->useLog('inventory')
                        ->event('transfer')
                        ->withProperties([
                            'old' => [
                                'source_warehouse' => $sourceWhName,
                                'source_quantity' => $srcQtyBefore,
                                'destination_warehouse' => $destWhName,
                                'destination_quantity' => $destQtyBefore,
                            ],
                            'attributes' => [
                                'source_warehouse' => $sourceWhName,
                                'source_quantity' => $srcQtyAfter,
                                'destination_warehouse' => $destWhName,
                                'destination_quantity' => $destQtyAfter,
                            ],
                            'product' => $product->name,
                            'sku' => $product->sku,
                            'quantity' => $qty,
                            'notes' => $notes
                        ])
                        ->log("Transfer stok produk '{$product->name}' ({$product->sku}) sebanyak {$qty} dari gudang '{$sourceWhName}' ke gudang '{$destWhName}'");

                } else {
                    $whId = $request->input('warehouse_id');
                    
                    $stock = Stock::firstOrCreate(
                        ['warehouse_id' => $whId, 'product_id' => $productId],
                        ['quantity' => 0, 'minimum_stock' => 10]
                    );

                    $qtyBefore = $stock->quantity;

                    if ($type === 'IN') {
                        $qtyChange = $qty;
                        $qtyAfter = $qtyBefore + $qty;
                    } elseif ($type === 'OUT') {
                        if ($qtyBefore < $qty) {
                            throw new \Exception('Stok di gudang terpilih tidak mencukupi.');
                        }
                        $qtyChange = -$qty;
                        $qtyAfter = $qtyBefore - $qty;
                    } else { // ADJUSTMENT
                        // Direct setting (we interpret quantity field as the new target quantity)
                        $qtyAfter = $qty;
                        $qtyChange = $qtyAfter - $qtyBefore;
                    }

                    $stock->update(['quantity' => $qtyAfter]);

                    StockMovement::create([
                        'warehouse_id' => $whId,
                        'product_id' => $productId,
                        'type' => $type,
                        'quantity_before' => $qtyBefore,
                        'quantity_change' => $qtyChange,
                        'quantity_after' => $qtyAfter,
                        'notes' => $notes,
                        'created_by' => $userId
                    ]);

                    // Log activity
                    $warehouse = Warehouse::find($whId);
                    $product = Product::find($productId);
                    
                    $eventMap = [
                        'IN' => 'stock_in',
                        'OUT' => 'stock_out',
                        'ADJUSTMENT' => 'adjustment',
                    ];
                    $event = $eventMap[$type] ?? 'adjustment';
                    
                    $actionDesc = match($type) {
                        'IN' => "Stok masuk produk '{$product->name}' ({$product->sku}) sebanyak {$qty} di gudang '{$warehouse->name}'",
                        'OUT' => "Stok keluar produk '{$product->name}' ({$product->sku}) sebanyak {$qty} di gudang '{$warehouse->name}'",
                        'ADJUSTMENT' => "Penyesuaian stok produk '{$product->name}' ({$product->sku}) di gudang '{$warehouse->name}' dari {$qtyBefore} menjadi {$qtyAfter} (selisih {$qtyChange})",
                    };

                    activity()
                        ->useLog('inventory')
                        ->event($event)
                        ->withProperties([
                            'old' => [
                                'quantity' => $qtyBefore,
                            ],
                            'attributes' => [
                                'quantity' => $qtyAfter,
                            ],
                            'product' => $product->name,
                            'sku' => $product->sku,
                            'warehouse' => $warehouse->name,
                            'quantity_change' => $qtyChange,
                            'notes' => $notes
                        ])
                        ->log($actionDesc);
                }
            });

            return redirect()->route('inventory.index')
                ->with('success', 'Mutasi stok berhasil diproses!')
                ->with('log_action', [
                    'category' => 'Stock Mutation',
                    'action' => "Stock {$type}",
                    'details' => "Mutasi stok {$type} selesai diproses."
                ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput()
                ->with('open_modal', 'adjustStockModal');
        }
    }
}
