<?php

namespace Database\Seeders;

use App\Models\FacilityFeedback;
use App\Models\SurveyResponse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SurveySeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi data survei simulasi.
     */
    public function run(): void
    {
        // 1. BUAT TEMPLATE SURVEI: FASILITAS IT & LAB
        $surveyIT = FacilityFeedback::create([
            'title' => 'Evaluasi Fasilitas IT & Lab Komputer',
            'description' => 'Survei periodik untuk menilai kualitas perangkat dan jaringan di lingkungan laboratorium UNMARIS.',
            'status' => 'active',
            'form_schema' => [
                [
                    'type' => 'rating',
                    'data' => [
                        'question' => 'Seberapa puas Anda dengan kecepatan internet di Lab Komputer?',
                        'is_required' => true
                    ]
                ],
                [
                    'type' => 'select',
                    'data' => [
                        'question' => 'Bagaimana kondisi PC/Laptop yang Anda gunakan saat praktikum?',
                        'options' => ['Sangat Baik', 'Normal', 'Sering Lag/Lemot', 'Rusak'],
                        'is_required' => true
                    ]
                ],
                [
                    'type' => 'textarea',
                    'data' => [
                        'question' => 'Saran atau kendala spesifik yang Anda temukan di Lab IT?',
                        'is_required' => false
                    ]
                ]
            ]
        ]);

        // 2. BUAT TEMPLATE SURVEI: KEBERSIHAN GEDUNG
        $surveyGedung = FacilityFeedback::create([
            'title' => 'Survei Kebersihan & Kenyamanan Gedung',
            'description' => 'Membantu Biro Sarpras menjaga standar kebersihan fasilitas umum kampus.',
            'status' => 'active',
            'form_schema' => [
                [
                    'type' => 'rating',
                    'data' => [
                        'question' => 'Nilai kebersihan toilet di Gedung Rektorat?',
                        'is_required' => true
                    ]
                ],
                [
                    'type' => 'rating',
                    'data' => [
                        'question' => 'Nilai kesejukan udara (AC) di ruang kelas?',
                        'is_required' => true
                    ]
                ],
                [
                    'type' => 'text',
                    'data' => [
                        'question' => 'Sebutkan Ruangan/Area yang menurut Anda paling kotor?',
                        'is_required' => true
                    ]
                ]
            ]
        ]);

        // 3. GENERATE JAWABAN SIMULASI (RESPON)
        $responders = [
            ['name' => 'Budi Santoso', 'type' => 'Mahasiswa'],
            ['name' => 'Dr. Maria Ulfa', 'type' => 'Dosen'],
            ['name' => 'Siska Putri', 'type' => 'Mahasiswa'],
            ['name' => 'Hendra Wijaya', 'type' => 'Staf/Tendik'],
            ['name' => null, 'type' => 'Tamu'], // Anonim
        ];

        // Isi Respon untuk Survei IT
        foreach ($responders as $index => $responder) {
            SurveyResponse::create([
                'facility_feedback_id' => $surveyIT->id,
                'responder_name' => $responder['name'],
                'responder_type' => $responder['type'],
                'answers' => [
                    'answer_0' => rand(3, 5), // Rating
                    'answer_1' => ['Sangat Baik', 'Normal', 'Sering Lag/Lemot'][rand(0, 2)], // Select
                    'answer_2' => 'Ini adalah komentar simulasi ke-' . ($index + 1) . ' untuk pengujian sistem.', // Textarea
                ],
                'created_at' => now()->subDays(rand(1, 10)),
            ]);
        }

        // Isi Respon untuk Survei Gedung
        foreach ($responders as $index => $responder) {
            SurveyResponse::create([
                'facility_feedback_id' => $surveyGedung->id,
                'responder_name' => $responder['name'],
                'responder_type' => $responder['type'],
                'answers' => [
                    'answer_0' => rand(1, 4), // Rating kebersihan cenderung variatif
                    'answer_1' => rand(2, 5), // Rating AC
                    'answer_2' => 'Ruang Kuliah B.20' . rand(1, 5), // Lokasi kotor
                ],
                'created_at' => now()->subHours(rand(1, 48)),
            ]);
        }

        $this->command->info('✅ Template Form Builder & Jawaban Simulasi berhasil di-seeding!');
    }
}