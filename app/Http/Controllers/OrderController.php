<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Status order yang valid, dalam urutan alur normal.
     * Dipakai untuk validasi transisi & tampilan badge di view.
     */
    private const STATUSES = ['pending', 'processing', 'checklist_mandor', 'checklist_kepala_lapangan', 'shipped', 'delivered', 'cancelled'];

    /**
     * Display a listing of the resource.
     *
     * Visibilitas order disesuaikan dengan role:
     * - Driver        : hanya order yang ditugaskan ke dirinya (driver_id).
     * - Mandor         : hanya order di gudang tempat ia ditugaskan (warehouse_id).
     * - Role lain      : melihat seluruh order (Owner, Admin, Kepala Produksi, Kepala Lapangan).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $status = $request->input('status');
        $warehouseId = $request->input('warehouse_id');

        $query = Order::with(['warehouse', 'driver', 'items']);

        if ($user->hasRole('Driver')) {
            $query->where('driver_id', $user->id);
        } elseif ($user->hasRole('Mandor')) {
            $query->where('warehouse_id', $user->warehouse_id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($warehouseId && $warehouseId !== 'all' && !$user->hasRole('Mandor')) {
            $query->where('warehouse_id', $warehouseId);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(10)->withQueryString();

        $warehouses = $user->hasRole('Mandor') ? collect() : Warehouse::where('status', true)->orderBy('name')->get();

        return view('orders.index', compact('orders', 'warehouses', 'status', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::where('status', true)->orderBy('name')->get();
        $drivers = User::role('Driver')->where('is_active', true)->orderBy('name')->get();

        return view('orders.create', compact('warehouses', 'drivers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * Header order + seluruh item dibuat dalam satu transaksi DB supaya
     * tidak ada kondisi order tanpa item (atau sebaliknya) jika terjadi
     * kegagalan di tengah proses.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        $data['order_number'] = $this->generateOrderNumber();
        $data['status'] = 'pending';
        $data['created_by'] = Auth::id();

        try {
            $order = DB::transaction(function () use ($data, $items) {
                $order = Order::create($data);

                foreach ($items as $item) {
                    $order->items()->create($this->prepareItemPayload($item));
                }

                return $order;
            });
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Gagal membuat order: ' . $e->getMessage()])
                ->withInput();
        }

        activity()
            ->useLog('order')
            ->performedOn($order)
            ->event('created')
            ->withProperties([
                'order_number' => $order->order_number,
                'warehouse' => $order->warehouse->name,
                'customer' => $order->customer_name,
                'item_count' => count($items),
            ])
            ->log("Order baru '{$order->order_number}' dibuat untuk pelanggan '{$order->customer_name}'");

        return redirect()
            ->route('order.show', $order)
            ->with('success', "Order {$order->order_number} berhasil dibuat!")
            ->with('log_action', [
                'category' => 'Order Management',
                'action' => 'Create Order',
                'details' => "Membuat order baru {$order->order_number} untuk {$order->customer_name}",
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $this->authorizeOrderAccess($order);

        $order->load(['warehouse', 'driver', 'createdBy', 'items.product', 'items.finishedProduct', 'checklists.checkedBy', 'checklists.photos']);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $this->authorizeOrderAccess($order);

        $order->load(['items.product', 'items.finishedProduct']);

        $warehouses = Warehouse::where('status', true)->orderBy('name')->get();
        $drivers = User::role('Driver')->where('is_active', true)->orderBy('name')->get();

        return view('orders.edit', compact('order', 'warehouses', 'drivers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * Strategi item: hapus seluruh item lama lalu insert ulang dari payload.
     * Lebih sederhana & aman daripada diff manual, dan order_items tidak
     * punya dependensi luar (stock_movements terpisah dari order_items),
     * sehingga hapus-insert ulang tidak merusak data lain.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $this->authorizeOrderAccess($order);

        if (in_array($order->status, ['shipped', 'delivered', 'cancelled'], true)) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Order yang sudah dikirim, selesai, atau dibatalkan tidak dapat diubah.']);
        }

        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        try {
            DB::transaction(function () use ($order, $data, $items) {
                $order->update($data);

                $order->items()->delete();

                foreach ($items as $item) {
                    $order->items()->create($this->prepareItemPayload($item));
                }
            });
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Gagal memperbarui order: ' . $e->getMessage()])
                ->withInput();
        }

        activity()
            ->useLog('order')
            ->performedOn($order)
            ->event('updated')
            ->withProperties(['order_number' => $order->order_number])
            ->log("Order '{$order->order_number}' diperbarui");

        return redirect()
            ->route('order.show', $order)
            ->with('success', "Order {$order->order_number} berhasil diperbarui!")
            ->with('log_action', [
                'category' => 'Order Management',
                'action' => 'Update Order',
                'details' => "Memperbarui order {$order->order_number}",
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * Hanya order dengan status 'pending' yang boleh dihapus permanen,
     * supaya order yang sudah mulai diproses tetap punya jejak histori.
     * Untuk membatalkan order yang sudah berjalan, gunakan updateStatus
     * ke status 'cancelled'.
     */
    public function destroy(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Hanya order dengan status pending yang dapat dihapus. Gunakan pembatalan untuk order lain.']);
        }

        $orderNumber = $order->order_number;
        $order->delete();

        activity()
            ->useLog('order')
            ->event('deleted')
            ->withProperties(['order_number' => $orderNumber])
            ->log("Order '{$orderNumber}' dihapus");

        return redirect()
            ->route('order.index')
            ->with('success', "Order {$orderNumber} berhasil dihapus!")
            ->with('log_action', [
                'category' => 'Order Management',
                'action' => 'Delete Order',
                'details' => "Menghapus order {$orderNumber}",
            ]);
    }

    /**
     * Assign or reassign a driver to the order.
     * Permission: order.assign_driver (Admin, Kepala Lapangan).
     */
    public function assignDriver(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => ['required', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $driver = User::findOrFail($request->input('driver_id'));

        if (!$driver->hasRole('Driver')) {
            return redirect()
                ->back()
                ->withErrors(['driver_id' => 'User terpilih bukan seorang Driver.']);
        }

        $oldDriver = $order->driver?->name ?? '-';
        $order->update(['driver_id' => $driver->id]);

        activity()
            ->useLog('order')
            ->performedOn($order)
            ->event('assign_driver')
            ->withProperties([
                'order_number' => $order->order_number,
                'old_driver' => $oldDriver,
                'new_driver' => $driver->name,
            ])
            ->log("Driver order '{$order->order_number}' diubah dari '{$oldDriver}' menjadi '{$driver->name}'");

        return redirect()
            ->back()
            ->with('success', "Driver {$driver->name} berhasil ditugaskan untuk order {$order->order_number}!")
            ->with('log_action', [
                'category' => 'Order Management',
                'action' => 'Assign Driver',
                'details' => "Menugaskan driver {$driver->name} ke order {$order->order_number}",
            ]);
    }

    /**
     * Update order status secara terkontrol.
     *
     * Transisi checklist_mandor & checklist_kepala_lapangan idealnya
     * terjadi otomatis lewat DeliveryChecklistController saat checklist
     * disetujui, bukan lewat endpoint ini. Endpoint ini terutama untuk
     * transisi manual: pending -> processing, processing -> shipped
     * (setelah checklist lolos), shipped -> delivered, atau -> cancelled.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $newStatus = $request->input('status');

        if ($order->status === $newStatus) {
            return redirect()
                ->back()
                ->withErrors(['status' => 'Order sudah berada pada status tersebut.']);
        }

        if (in_array($order->status, ['delivered', 'cancelled'], true)) {
            return redirect()
                ->back()
                ->withErrors(['status' => 'Order yang sudah selesai atau dibatalkan tidak dapat diubah statusnya.']);
        }

        $oldStatus = $order->status;
        $order->update(['status' => $newStatus]);

        activity()
            ->useLog('order')
            ->performedOn($order)
            ->event('status_change')
            ->withProperties([
                'order_number' => $order->order_number,
                'old' => ['status' => $oldStatus],
                'attributes' => ['status' => $newStatus],
            ])
            ->log("Status order '{$order->order_number}' diubah dari '{$oldStatus}' menjadi '{$newStatus}'");

        return redirect()
            ->back()
            ->with('success', "Status order {$order->order_number} berhasil diubah menjadi {$newStatus}!")
            ->with('log_action', [
                'category' => 'Order Management',
                'action' => 'Update Status',
                'details' => "Mengubah status order {$order->order_number} dari {$oldStatus} ke {$newStatus}",
            ]);
    }

    /**
     * Pastikan Driver/Mandor hanya bisa mengakses order yang relevan
     * untuk mereka, walau mereka tahu URL/ID order lain secara langsung.
     */
    private function authorizeOrderAccess(Order $order): void
    {
        $user = Auth::user();

        if ($user->hasRole('Driver') && $order->driver_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke order ini.');
        }

        if ($user->hasRole('Mandor') && $order->warehouse_id !== $user->warehouse_id) {
            abort(403, 'Order ini bukan milik gudang Anda.');
        }
    }

    /**
     * Normalisasi satu baris item dari request menjadi payload siap
     * disimpan ke OrderItem. Field yang tidak relevan dengan item_type
     * dikosongkan supaya data tetap bersih (mis. custom_width pada item
     * raw_material akan null, bukan 0 atau string kosong).
     */
    private function prepareItemPayload(array $item): array
    {
        $isFinished = $item['item_type'] === 'finished_product';

        return [
            'item_type' => $item['item_type'],
            'product_id' => $isFinished ? null : $item['product_id'] ?? null,
            'finished_product_id' => $isFinished ? $item['finished_product_id'] ?? null : null,
            'quantity_ordered' => $isFinished ? null : $item['quantity_ordered'] ?? null,
            'quantity_delivered' => null,
            'custom_width' => $isFinished ? $item['custom_width'] ?? null : null,
            'custom_height' => $isFinished ? $item['custom_height'] ?? null : null,
            'price_per_unit' => $item['price_per_unit'],
            'notes' => $item['notes'] ?? null,
        ];
    }

    /**
     * Generate nomor order unik dengan format ORD-{tahun}-{urutan 4 digit}.
     * Urutan dihitung per tahun, mengikuti pola seed data yang sudah ada
     * (ORD-2026-0001, ORD-2026-0002, dst).
     */
    private function generateOrderNumber(): string
    {
        $year = now()->year;

        $lastNumber = Order::where('order_number', 'like', "ORD-{$year}-%")
            ->orderBy('order_number', 'desc')
            ->value('order_number');

        $nextSequence = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

        return sprintf('ORD-%d-%04d', $year, $nextSequence);
    }
}
