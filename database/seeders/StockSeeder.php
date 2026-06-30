<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Warehouse;
class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Rentang stok disesuaikan per satuan unit produk, karena karakteristik
     * fisik bahan baku berbeda jauh: lembaran kaca/ACP besar & berat sehingga
     * stoknya wajar lebih sedikit, batang aluminium sedang, sementara
     * aksesoris kecil (Pcs/Tube) biasanya distok dalam jumlah besar.
     */
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        foreach ($warehouses as $warehouse) {
            foreach ($products as $product) {
                [$min, $max] = $this->stockRangeForUnit($product->unit);

                Stock::updateOrCreate(
                    [
                        'warehouse_id' => $warehouse->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => rand($min, $max),
                        'minimum_stock' => $this->minimumStockForUnit($product->unit),
                    ],
                );
            }
        }
    }

    /**
     * Rentang quantity realistis berdasarkan satuan unit bahan baku.
     */
    private function stockRangeForUnit(string $unit): array
    {
        return match ($unit) {
            'Lembar' => [10, 40], // kaca, ACP — besar & berat, stok terbatas
            'Batang' => [30, 100], // profil aluminium — sedang
            'Tube' => [50, 200], // sealant — kecil, mudah distok banyak
            'Pcs' => [80, 300], // aksesoris kecil (roda sliding, engsel, dll)
            default => [20, 150],
        };
    }

    /**
     * Minimum stock (ambang batas notifikasi) realistis per satuan unit.
     */
    private function minimumStockForUnit(string $unit): int
    {
        return match ($unit) {
            'Lembar' => 5,
            'Batang' => 15,
            'Tube' => 20,
            'Pcs' => 30,
            default => 20,
        };
    }
}
