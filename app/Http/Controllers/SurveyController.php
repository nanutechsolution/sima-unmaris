<?php

namespace App\Http\Controllers;

use App\Models\FacilityFeedback;
use App\Models\SurveyResponse;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    /**
     * Menampilkan halaman publik untuk satu template survei aktif.
     * Dilindungi berita acara IP address untuk mencegah pengiriman ganda (anonim).
     */
    public function show(string $id)
    {
        $survey = FacilityFeedback::query()
            ->where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

        $alreadySubmitted = SurveyResponse::query()
            ->where('facility_feedback_id', $survey->id)
            ->where('ip_address', request()->ip())
            ->exists();

        return view('frontend.dynamic-survey', [
            'survey' => $survey,
            'alreadySubmitted' => $alreadySubmitted,
        ]);
    }

    /**
     * Menyimpan jawaban survei dari pengguna publik.
     * Dilengkapi proteksi honeypot (website_url) dan throttle rate limit.
     */
    public function submit(Request $request, string $id)
    {
        // 1. CEK HONEYPOT (Jika terisi, berarti itu BOT/Spam)
        if (! empty($request->input('website_url'))) {
            abort(403, 'Aktivitas mencurigakan terdeteksi.');
        }

        $survey = FacilityFeedback::query()
            ->where('id', $id)
            ->where('status', 'active')
            ->firstOrFail();

        $validated = $request->validate([
            'answers' => 'required|array',
            'responder_type' => 'nullable|in:Mahasiswa,Dosen,Staf/Tendik,Tamu',
            'responder_name' => 'nullable|string|max:100',
        ]);

        // 2. CEK DUPLIKAT (Berdasarkan IP Address pada mode anonim publik)
        $alreadySubmitted = SurveyResponse::query()
            ->where('facility_feedback_id', $survey->id)
            ->where('ip_address', $request->ip())
            ->exists();

        if ($alreadySubmitted) {
            return back()->with('success', 'Anda sudah pernah mengisi survei ini. Terima kasih!');
        }

        // 3. SIMPAN RESPONS
        SurveyResponse::create([
            'facility_feedback_id' => $survey->id,
            'responder_name' => $validated['responder_name'] ?? $request->input('responder_name') ?? 'Anonim',
            'responder_type' => $validated['responder_type'] ?? $request->input('responder_type') ?? 'Mahasiswa',
            'answers' => $validated['answers'],
            'ip_address' => $request->ip(),
        ]);

        // 4. KIRIM NOTIFIKASI REAL-TIME KE ADMIN
        $admins = User::role(['super_admin'])->get();
        $reporterName = $request->input('responder_name') ?: 'Pengguna Anonim';

        Notification::make()
            ->title('📢 Respon Survei Baru')
            ->body("Respon masuk dari {$reporterName} untuk: {$survey->title}")
            ->success()
            ->sendToDatabase($admins);

        return back()->with('success', 'Terima kasih! Jawaban Anda telah dikirim.');
    }
}
