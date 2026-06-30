<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['delivery_checklist_id', 'photo_path', 'caption'];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(DeliveryChecklist::class, 'delivery_checklist_id');
    }

    /**
     * URL publik ke foto (asumsi disimpan di storage/app/public).
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->photo_path);
    }
}
