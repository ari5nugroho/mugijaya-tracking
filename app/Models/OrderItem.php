<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'item_type', 'product_id', 'finished_product_id', 'quantity_ordered', 'quantity_delivered', 'custom_width', 'custom_height', 'custom_area', 'price_per_unit', 'subtotal', 'notes'];

    protected $casts = [
        'custom_width' => 'float',
        'custom_height' => 'float',
        'custom_area' => 'float',
        'price_per_unit' => 'float',
        'subtotal' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (OrderItem $item) {
            if ($item->item_type === 'finished_product' && $item->custom_width && $item->custom_height) {
                $item->custom_area = round($item->custom_width * $item->custom_height, 4);

                $item->subtotal = round($item->custom_area * $item->price_per_unit, 2);
            } elseif ($item->item_type === 'raw_material' && $item->quantity_ordered) {
                $item->subtotal = round($item->quantity_ordered * $item->price_per_unit, 2);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke bahan baku standar (kaca lembaran, batang aluminium, dll).
     * Hanya terisi jika item_type = 'raw_material'.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke katalog fabrikasi custom (kusen, pintu kaca, dll).
     * Hanya terisi jika item_type = 'finished_product'.
     */
    public function finishedProduct(): BelongsTo
    {
        return $this->belongsTo(FinishedProduct::class);
    }

    public function isRawMaterial(): bool
    {
        return $this->item_type === 'raw_material';
    }

    public function isFinishedProduct(): bool
    {
        return $this->item_type === 'finished_product';
    }

    /**
     * Nama tampilan item, mengikuti jenisnya.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->isFinishedProduct() ? $this->finishedProduct?->name ?? '-' : $this->product?->name ?? '-';
    }
}
