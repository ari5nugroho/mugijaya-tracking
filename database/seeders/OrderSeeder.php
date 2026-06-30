<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use App\Models\FinishedProduct;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@mugijaya.com')->first();
        $driver = User::where('email', 'driver@mugijaya.com')->first();

        $whSemarang = Warehouse::where('code', 'WH-SMG-01')->first();
        $whJakarta = Warehouse::where('code', 'WH-JKT-02')->first();
        $whSurabaya = Warehouse::where('code', 'WH-SBY-03')->first();

        $products = Product::all();
        $finishedProducts = FinishedProduct::all();

        if ($products->isEmpty() || $finishedProducts->isEmpty() || !$whSemarang || !$whJakarta || !$whSurabaya) {
            $this->command->warn('⚠️ OrderSeeder dilewati: pastikan ProductSeeder, FinishedProductSeeder, dan WarehouseSeeder sudah dijalankan.');
            return;
        }

        $orders = [
            // 1. Order baru: pesan bahan baku saja (raw material), belum diproses
            [
                'order_number' => 'ORD-2026-0001',
                'warehouse_id' => $whSemarang->id,
                'customer_name' => 'Toko Bangunan Makmur Jaya',
                'customer_address' => 'Jl. Pandanaran No. 5, Semarang',
                'customer_phone' => '081234560001',
                'driver_id' => null,
                'status' => 'pending',
                'order_date' => now()->subDays(2),
                'delivery_date' => null,
                'notes' => 'Order baru, menunggu diproses Admin.',
                'created_by' => $admin?->id,
                'item_mix' => 'raw_only',
            ],

            // 2. Order campuran: bahan baku + fabrikasi custom, sudah masuk checklist mandor
            [
                'order_number' => 'ORD-2026-0002',
                'warehouse_id' => $whJakarta->id,
                'customer_name' => 'CV Sumber Rejeki',
                'customer_address' => 'Jl. Industri Raya No. 10, Jakarta',
                'customer_phone' => '081234560002',
                'driver_id' => $driver?->id,
                'status' => 'checklist_mandor',
                'order_date' => now()->subDays(1),
                'delivery_date' => null,
                'notes' => 'Sedang diperiksa oleh Mandor gudang Jakarta.',
                'created_by' => $admin?->id,
                'item_mix' => 'mixed',
            ],

            // 3. Order fabrikasi custom saja, lolos checklist mandor, menunggu kepala lapangan
            [
                'order_number' => 'ORD-2026-0003',
                'warehouse_id' => $whSurabaya->id,
                'customer_name' => 'Toko Elektronik Sinar Abadi',
                'customer_address' => 'Jl. Mayjen Sungkono No. 88, Surabaya',
                'customer_phone' => '081234560003',
                'driver_id' => $driver?->id,
                'status' => 'checklist_kepala_lapangan',
                'order_date' => now()->subDays(3),
                'delivery_date' => null,
                'notes' => 'Checklist mandor selesai, menunggu approval Kepala Lapangan.',
                'created_by' => $admin?->id,
                'item_mix' => 'finished_only',
            ],

            // 4. Order campuran, sudah dikirim
            [
                'order_number' => 'ORD-2026-0004',
                'warehouse_id' => $whSemarang->id,
                'customer_name' => 'Perumahan Graha Asri',
                'customer_address' => 'Jl. Setiabudi No. 21, Semarang',
                'customer_phone' => '081234560004',
                'driver_id' => $driver?->id,
                'status' => 'shipped',
                'order_date' => now()->subDays(5),
                'delivery_date' => now()->subDays(1),
                'notes' => 'Dalam perjalanan menuju pelanggan.',
                'created_by' => $admin?->id,
                'item_mix' => 'mixed',
            ],

            // 5. Order fabrikasi, sudah selesai diterima pelanggan
            [
                'order_number' => 'ORD-2026-0005',
                'warehouse_id' => $whJakarta->id,
                'customer_name' => 'Apartemen Green Tower',
                'customer_address' => 'Jl. Cawang Baru No. 7, Jakarta',
                'customer_phone' => '081234560005',
                'driver_id' => $driver?->id,
                'status' => 'delivered',
                'order_date' => now()->subDays(7),
                'delivery_date' => now()->subDays(4),
                'notes' => 'Barang sudah diterima dan dikonfirmasi pelanggan.',
                'created_by' => $admin?->id,
                'item_mix' => 'finished_only',
            ],

            // 6. Order dibatalkan
            [
                'order_number' => 'ORD-2026-0006',
                'warehouse_id' => $whSurabaya->id,
                'customer_name' => 'Toko Furniture Jati Indah',
                'customer_address' => 'Jl. Diponegoro No. 30, Surabaya',
                'customer_phone' => '081234560006',
                'driver_id' => null,
                'status' => 'cancelled',
                'order_date' => now()->subDays(6),
                'delivery_date' => null,
                'notes' => 'Dibatalkan atas permintaan pelanggan.',
                'created_by' => $admin?->id,
                'item_mix' => 'raw_only',
            ],
        ];

        foreach ($orders as $data) {
            $itemMix = $data['item_mix'];
            unset($data['item_mix']);

            $order = Order::updateOrCreate(['order_number' => $data['order_number']], $data);

            if ($order->items()->count() > 0) {
                continue;
            }

            $isDelivered = in_array($order->status, ['shipped', 'delivered']);

            // --- Item raw material ---
            if (in_array($itemMix, ['raw_only', 'mixed'])) {
                $rawCount = $itemMix === 'mixed' ? 1 : 2;
                $selectedProducts = $products->random(min($rawCount, $products->count()));

                foreach ($selectedProducts as $product) {
                    $qty = rand(5, 30);
                    $pricePerUnit = $product->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_type' => 'raw_material',
                        'product_id' => $product->id,
                        'quantity_ordered' => $qty,
                        'quantity_delivered' => $isDelivered ? $qty : null,
                        'price_per_unit' => $pricePerUnit,
                        // subtotal dihitung otomatis lewat event saving() di model OrderItem
                    ]);
                }
            }

            // --- Item finished product (fabrikasi custom) ---
            if (in_array($itemMix, ['finished_only', 'mixed'])) {
                $fpCount = $itemMix === 'mixed' ? 1 : 2;
                $selectedFinished = $finishedProducts->random(min($fpCount, $finishedProducts->count()));

                foreach ($selectedFinished as $fp) {
                    $width = round(rand(80, 250) / 100, 2); // 0.8m - 2.5m
                    $height = round(rand(100, 300) / 100, 2); // 1.0m - 3.0m

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_type' => 'finished_product',
                        'finished_product_id' => $fp->id,
                        'custom_width' => $width,
                        'custom_height' => $height,
                        'price_per_unit' => $fp->price_per_m2,
                        'quantity_delivered' => $isDelivered ? 1 : null,
                        // custom_area & subtotal dihitung otomatis lewat event saving()
                    ]);
                }
            }
        }

        $this->command->info('✅ Dummy orders & order items (raw material + finished product) berhasil dibuat!');
    }
}
