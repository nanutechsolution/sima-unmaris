<?php

namespace Database\Seeders;

use App\Models\FacilityFeedback;
use App\Models\SurveyResponse;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SurveySeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi data survei simulasi skala besar.
     */
    public function run(): void
    {
        // 1. DAFTAR TEMPLATE SURVEI (MASTER)
        $templates = [
            [
                'title' => 'Evaluasi Fasilitas IT & Lab Komputer',
                'description' => 'Menilai kualitas perangkat keras, perangkat lunak, dan jaringan internet di laboratorium.',
                'status' => 'active',
                'schema' => [
                    ['type' => 'rating', 'question' => 'Kecepatan internet wifi di area Lab?'],
                    ['type' => 'select', 'question' => 'Kondisi PC saat digunakan?', 'options' => ['Sangat Baik', 'Normal', 'Sering Lag', 'Rusak']],
                    ['type' => 'textarea', 'question' => 'Saran spesifik untuk Lab IT?']
                ]
            ],
            [
                'title' => 'Survei Kebersihan Gedung & Fasilitas Umum',
                'description' => 'Membantu Biro Sarpras menjaga standar kebersihan lingkungan kampus UNMARIS.',
                'status' => 'active',
                'schema' => [
                    ['type' => 'rating', 'question' => 'Kebersihan toilet Gedung Rektorat?'],
                    ['type' => 'rating', 'question' => 'Kesejukan AC di ruang kelas?'],
                    ['type' => 'text', 'question' => 'Area yang menurut Anda paling kotor?']
                ]
            ],
            [
                'title' => 'Layanan Keamanan & Area Parkir',
                'description' => 'Survei mengenai kinerja Satpam dan kapasitas area parkir kendaraan.',
                'status' => 'active',
                'schema' => [
                    ['type' => 'rating', 'question' => 'Keramahan petugas keamanan (Satpam)?'],
                    ['type' => 'select', 'question' => 'Kemudahan mencari slot parkir?', 'options' => ['Mudah', 'Cukup Sulit', 'Sangat Penuh']],
                    ['type' => 'rating', 'question' => 'Rasa aman meninggalkan helm di motor?']
                ]
            ],
            [
                'title' => 'DRAFT: Survei Kantin Sehat',
                'description' => 'Rencana survei untuk menilai harga dan kebersihan makanan di kantin.',
                'status' => 'draft',
                'schema' => [
                    ['type' => 'rating', 'question' => 'Harga makanan dibanding porsi?'],
                    ['type' => 'rating', 'question' => 'Kebersihan peralatan makan?']
                ]
            ]
        ];

        $responderTypes = ['Mahasiswa', 'Dosen', 'Staf/Tendik', 'Tamu'];
        $names = ['Ahmad', 'Budi', 'Siska', 'Maria', 'Hendra', 'Dewi', 'Rizky', 'Lely', 'Yusuf', 'Nina', null];

        foreach ($templates as $t) {
            // Transformasi schema ke format JSON Filament Builder
            $formSchema = collect($t['schema'])->map(function ($item) {
                $base = [
                    'type' => $item['type'],
                    'data' => [
                        'question' => $item['question'],
                        'is_required' => true
                    ]
                ];
                if (isset($item['options'])) {
                    $base['data']['options'] = $item['options'];
                }
                return $base;
            })->toArray();

            $survey = FacilityFeedback::create([
                'title' => $t['title'],
                'description' => $t['description'],
                'status' => $t['status'],
                'form_schema' => $formSchema,
            ]);

            // Hanya generate jawaban jika status survei adalah 'active'
            if ($survey->status === 'active') {
                $responseCount = rand(15, 25); // Antara 15-25 respon per survei

                for ($i = 0; $i < $responseCount; $i++) {
                    $answers = [];
                    foreach ($formSchema as $index => $field) {
                        $key = 'answer_' . $index;
                        
                        $answers[$key] = match($field['type']) {
                            'rating' => rand(2, 5), // Kebanyakan puas (2-5 bintang)
                            'select' => $field['data']['options'][rand(0, count($field['data']['options']) - 1)],
                            'text' => 'Contoh temuan lapangan ke-' . ($i + 1),
                            'textarea' => 'Saran perbaikan untuk ' . $survey->title . ' agar lebih baik lagi di masa depan.',
                            default => null
                        };
                    }

                    SurveyResponse::create([
                        'facility_feedback_id' => $survey->id,
                        'responder_name' => $names[rand(0, count($names) - 1)],
                        'responder_type' => $responderTypes[rand(0, count($responderTypes) - 1)],
                        'answers' => $answers,
                        'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                    ]);
                }
            }
        }

        $this->command->info('✅ Skala Besar: Template & Puluhan Respon Berhasil Di-generate!');
    }
}