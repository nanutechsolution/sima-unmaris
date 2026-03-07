<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityFeedback extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'facility_feedbacks';

    protected $fillable = [
        'title',
        'description',
        'form_schema',
        'status',
    ];

    /**
     * Konversi tipe data otomatis (Casting).
     * Sangat penting untuk Filament Builder agar JSON dari database 
     * dibaca sebagai Array di dalam aplikasi.
     */
    protected $casts = [
        'form_schema' => 'array',
    ];


    /**
     * Relasi ke SurveyResponse (jawaban survei).
     * Satu Template Survei bisa memiliki banyak Respon.
     */
    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'facility_feedback_id');
    }
}
