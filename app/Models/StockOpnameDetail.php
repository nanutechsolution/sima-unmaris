<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_found' => 'boolean',
        'scanned_at' => 'datetime',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function actualRoom()
    {
        return $this->belongsTo(Room::class, 'actual_room_id');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}