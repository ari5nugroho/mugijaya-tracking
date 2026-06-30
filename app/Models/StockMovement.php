<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Stock;

class StockMovement extends Model
{
    /**
     * Dipanggil setiap kali StockMovement baru dibuat.
     *
     * Tabel `stocks` berfungsi sebagai cache saldo real-time, sementara
     * `stock_movements` adalah ledger/histori lengkap (termasuk GPS).
     * Setiap insert ke stock_movements WAJIB mengupdate saldo di stocks
     * supaya kedua tabel selalu konsisten.
     */
    /**
     * Register model creating event to keep stocks table in sync.
     *
     * Note: cannot name this method `creating` because Model::creating()
     * exists for registering callbacks. Use booted to attach our handler.
     */
    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (StockMovement $movement) {
            DB::transaction(function () use ($movement) {
                // Lock baris stock supaya aman dari race condition saat banyak
                // movement terjadi bersamaan (misal beberapa Mandor input stok
                // di waktu yang sama).
                $stock = Stock::lockForUpdate()->firstOrCreate(
                    [
                        'warehouse_id' => $movement->warehouse_id,
                        'product_id' => $movement->product_id,
                    ],
                    [
                        'quantity' => 0,
                        'minimum_stock' => 0,
                    ]
                );

                $quantityBefore = $stock->quantity;
                $quantityAfter = (new static)->calculateQuantityAfter($movement->type, $quantityBefore, $movement->quantity_change);

                if ($quantityAfter < 0) {
                    throw ValidationException::withMessages([
                        'quantity_change' => "Stok tidak cukup. Saldo saat ini: {$quantityBefore}, percobaan pengurangan: {$movement->quantity_change}.",
                    ]);
                }

                // Isi otomatis quantity_before & quantity_after di movement,
                // supaya controller tidak perlu hitung manual & data selalu akurat.
                $movement->quantity_before = $quantityBefore;
                $movement->quantity_after = $quantityAfter;

                $stock->quantity = $quantityAfter;
                $stock->save();
            });
        });
    }
 
    /**
     * Hitung saldo akhir berdasarkan jenis movement.
     *
     * IN & ADJUSTMENT(+)  -> quantity_change dianggap menambah saldo
     * OUT & TRANSFER      -> quantity_change dianggap mengurangi saldo
     *
     * Catatan: quantity_change disimpan sebagai nilai absolut (positif)
     * di sisi caller; arah +/- ditentukan oleh `type`.
     */
    private function calculateQuantityAfter(string $type, int $quantityBefore, int $quantityChange): int
    {
        return match ($type) {
            'IN' => $quantityBefore + abs($quantityChange),
            'OUT', 'TRANSFER' => $quantityBefore - abs($quantityChange),
            'ADJUSTMENT' => $quantityBefore + $quantityChange, // bisa +/- sesuai input asli
            default => $quantityBefore,
        };
    }
}
