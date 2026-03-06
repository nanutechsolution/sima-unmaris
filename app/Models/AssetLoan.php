<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetLoan extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'loan_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'actual_return_date' => 'datetime',
    ];

    /**
     * MAGIC HOOK: Otomatis ubah status aset menjadi "IN_USE" 
     * di database utama ketika proses peminjaman baru dibuat.
     */
    protected static function booted()
    {
        static::created(function ($loan) {
            if ($loan->asset) {
                $loan->asset->update(['status' => \App\Enums\AssetStatusEnum::IN_USE]);
            }
        });
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function borrower()
    {
        return $this->belongsTo(User::class, 'borrower_user_id');
    }
}