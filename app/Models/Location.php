<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function assets()
{
    return $this->hasManyThrough(Asset::class, Room::class);
}
}
