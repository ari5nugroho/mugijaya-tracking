<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FinishedProduct extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['category_id', 'code', 'name', 'price_per_m2', 'material_notes', 'description', 'status'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'name', 'price_per_m2', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('finished_product')
            ->setDescriptionForEvent(
                fn(string $eventName) => match ($eventName) {
                    'created' => "Jenis fabrikasi baru '{$this->name}' ({$this->code}) dibuat",
                    'updated' => "Jenis fabrikasi '{$this->name}' ({$this->code}) diperbarui",
                    'deleted' => "Jenis fabrikasi '{$this->name}' ({$this->code}) dihapus",
                    default => "Aksi {$eventName} pada jenis fabrikasi '{$this->name}'",
                },
            );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'finished_product_id');
    }
}
