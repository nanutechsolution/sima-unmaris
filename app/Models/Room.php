<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'location_id',
        'name',
        'code',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}