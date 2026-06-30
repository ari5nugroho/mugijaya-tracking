<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use LogsActivity;

    protected $fillable = ['code', 'name', 'address', 'capacity', 'status', 'manager', 'capacity_used', 'latitude', 'longitude'];

    /**
     * Spatie Activitylog configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'name', 'address', 'capacity', 'status', 'manager'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('warehouse')
            ->setDescriptionForEvent(
                fn(string $eventName) => match ($eventName) {
                    'created' => "Gudang baru '{$this->name}' ({$this->code}) dibuat",
                    'updated' => "Data gudang '{$this->name}' ({$this->code}) diperbarui",
                    'deleted' => "Gudang '{$this->name}' ({$this->code}) dihapus",
                    default => "Aksi {$eventName} pada gudang '{$this->name}'",
                },
            );
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Histori pergerakan stok (in/out/transfer/adjustment) di gudang ini.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * User dengan role Mandor yang ditugaskan di gudang ini.
     * Catatan: ini tidak memfilter role secara otomatis — filter role
     * sebaiknya dilakukan di query, contoh: $warehouse->users()->role('Mandor')->get()
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Order yang berasal/dikirim dari gudang ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
