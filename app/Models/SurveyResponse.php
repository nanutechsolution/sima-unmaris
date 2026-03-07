<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyResponse extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'facility_feedback_id',
        'responder_name',
        'responder_type',
        'answers',
        'ip_address',
    ];

    /**
     * Otomatis ubah JSON di database menjadi Array di aplikasi.
     */
    protected $casts = [
        'answers' => 'array',
    ];

    /**
     * Relasi balik ke Template Surveinya.
     */
    public function survey()
    {
        return $this->belongsTo(FacilityFeedback::class, 'facility_feedback_id');
    }
}