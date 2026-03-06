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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class)->orderBy('maintenance_date', 'desc');
    }
    public function loans()
    {
        return $this->hasMany(AssetLoan::class)->orderBy('loan_date', 'desc');
    }

    public function getCurrentValueAttribute()
    {
        // Jika tidak ada harga beli atau tanggal beli, kembalikan harga aslinya/0
        if (!$this->acquisition_value || !$this->acquisition_date || !$this->useful_life_years) {
            return $this->acquisition_value ?? 0;
        }

        // 1. Hitung sudah berapa tahun aset ini dibeli hingga hari ini
        $yearsPassed = $this->acquisition_date->diffInYears(now());

        // 2. Jika umur aset sudah melewati batas umur ekonomis, nilainya Rp 0 (atau nilai sisa)
        if ($yearsPassed >= $this->useful_life_years) {
            return 0;
        }

        // 3. Hitung penyusutan per tahun (Harga Beli / Umur Ekonomis)
        $annualDepreciation = $this->acquisition_value / $this->useful_life_years;

        // 4. Hitung total penyusutan yang sudah terjadi
        $accumulatedDepreciation = $annualDepreciation * $yearsPassed;

        // 5. Nilai Buku Saat Ini = Harga Beli - Total Penyusutan
        $currentValue = $this->acquisition_value - $accumulatedDepreciation;

        // Pastikan tidak minus
        return max(0, $currentValue);
    }
}
