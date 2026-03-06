<?php

namespace App\Models;

use App\Enums\AssetConditionEnum;
use App\Enums\AssetStatusEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Asset extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'room_id',
        'supplier_id',
        'pic_user_id',
        'acquisition_value',
        'acquisition_date',
        'status',
        'condition',
        'qr_signature_hash',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_value' => 'decimal:2',
        'status' => AssetStatusEnum::class,
        'condition' => AssetConditionEnum::class,
    ];

    // --- AUDIT TRAIL CONFIGURATION ---
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Log semua field fillable yang berubah
            ->logOnlyDirty() // Hanya log jika ada perubahan (hemat storage)
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Aset telah di-{$eventName}");
    }

    // --- RELATIONS ---
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function handovers(): HasMany
    {
        return $this->hasMany(AssetHandover::class)->orderBy('handover_time', 'desc');
    }
}
