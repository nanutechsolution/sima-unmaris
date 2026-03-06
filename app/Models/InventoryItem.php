<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit',
        'current_stock',
        'min_stock',
        'description',
    ];

    /**
     * Relasi ke transaksi mutasi stok.
     */
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}