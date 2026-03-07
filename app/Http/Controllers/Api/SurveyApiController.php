<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacilityFeedback;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

/**
 * SurveyApiController
 * Menangani logika backend untuk Portal Survei Terintegrasi (React)
 * dengan standar keamanan Enterprise.
 */
class SurveyApiController extends Controller
{
    /**
     * LOGIN VIA SIAKAD API
     * Mengotentikasi user dan mengembalikan Token (Sanctum).
     */
    public function login(Request $request)
    {
        $request->validate([
            'nim_nidn' => 'required',
            'password' => 'required',
        ]);

        /**
         * SIMULASI INTEGRASI API SIAKAD
         * Dalam produksi, Anda akan melakukan POST ke endpoint SIAKAD 
         * untuk memverifikasi kredensial mahasiswa/dosen.
         */
        // $response = Http::post('https://siakad.unmaris.ac.id/api/v1/verify', $request->all());

        // Simulasi pencarian user di database lokal yang sudah tersinkron dengan SIAKAD
        $user = User::where('email', $request->nim_nidn . '@unmaris.ac.id')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Kredensial SIAKAD tidak valid.'
            ], 401);
        }

        // Generate Token Akses Aman
        $token = $user->createToken('survey_access_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->roles()->first()?->name ?? 'Mahasiswa',
            ],
            'token' => $token,
        ]);
    }

    /**
     * AMBIL DETAIL SURVEI & STATUS USER
     * Mengecek apakah user yang login sudah pernah mengisi atau belum.
     */
    public function getSurveyDetails(Request $request, $id)
    {
        $survey = FacilityFeedback::where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

        // Cek partisipasi berdasarkan User ID (Keamanan Tinggi)
        $hasSubmitted = SurveyResponse::where('facility_feedback_id', $id)
            ->where('user_id', $request->user()->id)
            ->exists();

        return response()->json([
            'survey' => $survey,
            'has_submitted' => $hasSubmitted,
            'server_time' => now()->toIso8601String()
        ]);
    }

    /**
     * SUBMIT JAWABAN SURVEI
     * Dilengkapi proteksi double-submission dan audit trail.
     */
    public function submitResponse(Request $request, $id)
    {
        $survey = FacilityFeedback::findOrFail($id);
        $user = $request->user();

        // 1. Validasi Double Submission (Mencegah Bypass Client-side)
        $exists = SurveyResponse::where('facility_feedback_id', $id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Anda sudah berpartisipasi dalam survei ini.'
            ], 422);
        }

        // 2. Validasi Data
        $request->validate([
            'answers' => 'required|array',
        ]);

        // 3. Simpan Jawaban dengan Metadata Lengkap
        $response = SurveyResponse::create([
            'facility_feedback_id' => $survey->id,
            'user_id' => $user->id, // Mengunci ke akun login
            'responder_name' => $user->name,
            'responder_type' => $user->roles()->first()?->name ?? 'Mahasiswa',
            'answers' => $request->answers,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // 4. Kirim Notifikasi ke Admin (Real-time via Filament)
        $admins = User::role('Super Admin')->get();
        Notification::make()
            ->title('Respon Survei Terverifikasi')
            ->body("{$user->name} baru saja mengirimkan respon untuk {$survey->title}")
            ->success()
            ->sendToDatabase($admins);

        return response()->json([
            'message' => 'Terima kasih, jawaban Anda telah terekam secara permanen.',
            'reference_id' => $response->id
        ], 201);
    }
}
