<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'order_number',
        'warehouse_id',
        'customer_name',
        'customer_address',
        'customer_phone',
        'driver_id',
        'status',
        'order_date',
        'delivery_date',
        'notes',
        'created_by',
    ];
 
    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
    ];
 
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
 
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
 
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
 
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
 
    public function checklists(): HasMany
    {
        return $this->hasMany(DeliveryChecklist::class);
    }
 
    public function checklistMandor()
    {
        return $this->hasOne(DeliveryChecklist::class)->where('layer', 'mandor');
    }
 
    public function checklistKepalaLapangan()
    {
        return $this->hasOne(DeliveryChecklist::class)->where('layer', 'kepala_lapangan');
    }
}
