<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Warehouse extends Model
{
    use LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'address',
        'capacity',
        'status',
        'manager',
        'capacity_used',
    ];

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
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => "Gudang baru '{$this->name}' ({$this->code}) dibuat",
                'updated' => "Data gudang '{$this->name}' ({$this->code}) diperbarui",
                'deleted' => "Gudang '{$this->name}' ({$this->code}) dihapus",
                default   => "Aksi {$eventName} pada gudang '{$this->name}'",
            });
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
