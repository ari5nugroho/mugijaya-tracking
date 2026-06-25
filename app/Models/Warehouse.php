<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    //
    protected $fillable = [
        'code',
        'name',
        'address',
        'capacity',
        'status',
        'manager',
        'capacity_used',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
