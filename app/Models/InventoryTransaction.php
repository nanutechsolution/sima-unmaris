<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected static function booted()
    {
        // Pemicu otomatis saat transaksi baru dibuat
        static::created(function ($transaction) {
            $item = $transaction->item;
            
            if ($transaction->type === 'in') {
                $item->increment('current_stock', $transaction->quantity);
            } else {
                $item->decrement('current_stock', $transaction->quantity);
            }
        });
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}