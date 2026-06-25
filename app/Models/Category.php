<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'description', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('category')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => "Kategori '{$this->name}' dibuat",
                'updated' => "Kategori '{$this->name}' diperbarui",
                'deleted' => "Kategori '{$this->name}' dihapus",
                default   => "Aksi {$eventName} pada kategori '{$this->name}'",
            });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
