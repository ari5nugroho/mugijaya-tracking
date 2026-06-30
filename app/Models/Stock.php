<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Warehouse;
use App\Models\Product;

class Stock extends Model
{
    protected $fillable = ['warehouse_id', 'product_id', 'quantity', 'minimum_stock'];

    protected $casts = [
        'quantity' => 'integer',
        'minimum_stock' => 'integer',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Cek apakah stok sudah di bawah atau sama dengan ambang batas minimum.
     * Dipakai untuk notifikasi/alert ke Kepala Lapangan & Kepala Produksi.
     */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_stock;
    }
}
