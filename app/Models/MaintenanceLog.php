<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'problem_description',
        'action_taken',
        'cost',
        'performed_by',
        'status',
        'receipt_photo_path',
        'notes',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}