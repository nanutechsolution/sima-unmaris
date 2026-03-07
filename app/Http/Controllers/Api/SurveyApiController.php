<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FacilityFeedback;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class SurveyApiController extends Controller
{
    /**
     * AMBIL DAFTAR SURVEI UNTUK DASHBOARD
     * Fitur baru: Mahasiswa bisa melihat semua survei yang tersedia.
     */
    public function getAvailableSurveys(Request $request)
    {
        $userId = $request->user()->id;

        $surveys = FacilityFeedback::where('status', 'active')
            ->latest()
            ->get()
            ->map(function ($survey) use ($userId) {
                // Cek status partisipasi untuk setiap survei
                $hasSubmitted = SurveyResponse::where('facility_feedback_id', $survey->id)
                    ->where('user_id', $userId)
                    ->exists();

                return [
                    'id' => $survey->id,
                    'title' => $survey->title,
                    'description' => $survey->description,
                    'has_submitted' => $hasSubmitted,
                ];
            });

        return response()->json([
            'surveys' => $surveys
        ]);
    }

    /**
     * LOGIN BRIDGE: SIMA -> SIAKAD
     */
    public function login(Request $request)
    {
        $request->validate([
            'nim_nidn' => 'required|string',
            'password' => 'required',
        ]);

        try {
            $baseUrl = rtrim(config('services.siakad.url'), '/');
            $apiKey = config('services.siakad.key');

            $response = Http::timeout(5)->withHeaders([
                'X-SIMA-KEY' => $apiKey,
                'Accept' => 'application/json',
            ])->post($baseUrl . '/login', [
                'username' => $request->nim_nidn,
                'password' => $request->password,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'message' => $response->json('message') ?? 'SIAKAD merespon dengan error.',
                ], $response->status());
            }

            $siakadToken = $response->json('token');
            $profileResponse = Http::withToken($siakadToken)->get($baseUrl . '/user/me');
            $siakadUser = $profileResponse->json('data');

            $user = User::updateOrCreate(
                ['email' => $siakadUser['identifier'] . '@unmaris.ac.id'],
                [
                    'name' => $siakadUser['name'],
                    'password' => bcrypt(\Str::random(16)),
                    'username_siakad' => $siakadUser['identifier'],
                    'role_name' => $siakadUser['role'],
                ]
            );

            $localToken = $user->createToken('portal_survei_access')->plainTextToken;

            return response()->json([
                'user' => [
                    'name' => $user->name,
                    'role' => $user->role_name,
                    'identifier' => $user->username_siakad,
                ],
                'token' => $localToken,
            ]);

        } catch (\Exception $e) {
            Log::error('SIAKAD Bridge Error: ' . $e->getMessage());
            return response()->json(['message' => 'Kesalahan koneksi ke SIAKAD.'], 500);
        }
    }

    /**
     * AMBIL DETAIL SURVEI SPESIFIK
     */
    public function getSurveyDetails(Request $request, $id)
    {
        $survey = FacilityFeedback::where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

        $hasSubmitted = SurveyResponse::where('facility_feedback_id', $id)
            ->where('user_id', $request->user()->id)
            ->exists();

        return response()->json([
            'survey' => [
                'id' => $survey->id,
                'title' => $survey->title,
                'description' => $survey->description,
                'schema' => $survey->form_schema,
            ],
            'has_submitted' => $hasSubmitted,
        ]);
    }

    /**
     * SIMPAN RESPON SURVEI
     */
    public function submitResponse(Request $request, $id)
    {
        $survey = FacilityFeedback::where('id', $id)->where('status', 'active')->firstOrFail();

        $alreadySubmitted = SurveyResponse::where('facility_feedback_id', $id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($alreadySubmitted) {
            return response()->json(['message' => 'Anda sudah berpartisipasi.'], 403);
        }

        $request->validate(['answers' => 'required|array']);

        SurveyResponse::create([
            'facility_feedback_id' => $id,
            'user_id' => $request->user()->id,
            'responder_name' => $request->user()->name,
            'responder_type' => $request->user()->role_name ?? 'Mahasiswa',
            'answers' => $request->answers,
            'ip_address' => $request->ip(),
        ]);

        $admins = User::role(['Super Admin'])->get();
        Notification::make()
            ->title('📢 Respon Survei Baru')
            ->body("Respon masuk dari {$request->user()->name} untuk: {$survey->title}")
            ->success()
            ->sendToDatabase($admins);

        return response()->json(['message' => 'Jawaban Anda telah diverifikasi dan disimpan.']);
    }
}