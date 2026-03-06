<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasUuids;
    protected $guarded = [];

    // Relasi ke Kepala Departemen (User)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Relasi ke Aset
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }


    /**
     * Relasi ke mutasi inventori.
     */
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
