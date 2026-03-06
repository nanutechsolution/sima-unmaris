<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssetHandover extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'asset_id',
        'giver_user_id',
        'receiver_user_id',
        'location_id',
        'handover_time',
        'asset_photo_path',
        'document_photo_path',
        'notes',
        'digital_signature_hash',
    ];

    protected $casts = [
        'handover_time' => 'datetime',
    ];

    // --- AUDIT TRAIL CONFIGURATION ---
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Serah terima aset di-{$eventName}");
    }

    // --- RELATIONS ---
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function giver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'giver_user_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}