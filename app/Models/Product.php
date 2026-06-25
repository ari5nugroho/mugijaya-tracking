<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use LogsActivity;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'weight',
        'length',
        'width',
        'height',
        'description',
        'status',
        'price',
        'unit',
    ];

    /**
     * Spatie Activitylog configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'category_id',
                'sku',
                'name',
                'weight',
                'length',
                'width',
                'height',
                'description',
                'status',
                'price',
                'unit',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('product')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => "Produk baru '{$this->name}' ({$this->sku}) dibuat",
                'updated' => "Data produk '{$this->name}' ({$this->sku}) diperbarui",
                'deleted' => "Produk '{$this->name}' ({$this->sku}) dihapus",
                default   => "Aksi {$eventName} pada produk '{$this->name}'",
            });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
