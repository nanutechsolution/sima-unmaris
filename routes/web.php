<?php

use App\Models\Asset;
use App\Models\FacilityFeedback;
use App\Models\MaintenanceLog;
use App\Models\SurveyResponse;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/verify-asset/{signature}', function (string $signature) {
    // Cari aset berdasarkan digital signature yang terenkripsi di QR
    $asset = Asset::with(['category', 'room.location', 'pic'])
        ->where('qr_signature_hash', $signature)
        ->firstOrFail();

    // Mengembalikan tampilan halaman publik (Frontend) dengan membawa data $asset
    return view('frontend.asset-verify', compact('asset'));
})->name('asset.verify');


Route::get('/verify-asset/{signature}/report', function (string $signature) {
    $asset = Asset::where('qr_signature_hash', $signature)->firstOrFail();
    return view('frontend.report-damage', compact('asset'));
})->name('asset.report');
// Tambahkan middleware 'throttle:3,1' (Maksimal 3 kali lapor dalam 1 menit per IP)
Route::post('/verify-asset/{signature}/report', function (Request $request, string $signature) {
    // 1. CEK HONEYPOT (Jika terisi, berarti itu BOT/Spam)
    if (!empty($request->website_url)) {
        abort(403, 'Aktivitas mencurigakan terdeteksi.');
    }
    $asset = Asset::where('qr_signature_hash', $signature)->firstOrFail();
    $request->validate([
        'problem_description' => 'required|string|max:255',
        'reporter_name' => 'nullable|string|max:100',
    ]);
    MaintenanceLog::create([
        'asset_id' => $asset->id,
        'maintenance_date' => now(),
        'problem_description' => '[LAPORAN QR] ' . $request->problem_description,
        'status' => 'scheduled',
        // Menyimpan IP Address si pelapor di catatan agar admin tahu
        'notes' => 'Dilaporkan oleh: ' . ($request->reporter_name ?: 'Anonim') . ' | IP: ' . $request->ip(),
    ]);
    // 3. KIRIM NOTIFIKASI REAL-TIME KE ADMIN & STAF
    // Mengambil semua user yang memiliki role Super Admin atau Staf Operasional
    $admins = User::role(['Super Admin', 'Staf Operasional'])->get();

    // Tembakkan notifikasi ke database
    Notification::make()
        ->title('🚨 Laporan Kerusakan Baru!')
        ->body("Aset {$asset->asset_code} ({$asset->name}) dilaporkan rusak oleh " . ($request->reporter_name ?: 'pengguna anonim') . ".")
        ->icon('heroicon-o-exclamation-triangle')
        ->warning()
        ->sendToDatabase($admins);
    return redirect()->route('asset.verify', $signature)
        ->with('success', 'Terima kasih! Laporan kerusakan telah terkirim.');
})->name('asset.report.submit')->middleware('throttle:3,1'); // Limit 3 hit / menit
Route::get('/survei/{id}', function (Request $request, $id) {
    $survey = FacilityFeedback::where('id', $id)
        ->where('status', 'active')
        ->firstOrFail();

    // LOGIKA KEAMANAN: Cek apakah IP ini sudah ada di tabel jawaban untuk survei ini
    $alreadySubmitted = SurveyResponse::where('facility_feedback_id', $id)
        ->where('ip_address', $request->ip())
        ->exists();

    return view('frontend.dynamic-survey', [
        'survey' => $survey,
        'alreadySubmitted' => $alreadySubmitted // Kirim status ke view
    ]);
})->name('survey.show');

    // Cari survei berdasarkan ID dan pastikan statusnya 'active'
    $survey = FacilityFeedback::where('id', $id)
        ->where('status', 'active')
        ->firstOrFail();

    return view('frontend.dynamic-survey', compact('survey'));
})->name('survey.show');
Route::post('/survei/{id}', function (Request $request, $id) {
    // Cek Anti-Spam
    if (!empty($request->website_url)) {
        abort(403, 'Aktivitas mencurigakan terdeteksi.');
    }
    $survey = FacilityFeedback::where('id', $id)->where('status', 'active')->firstOrFail();

    $request->validate([
        'responder_name' => 'nullable|string|max:100',
        'responder_type' => 'required|string',
        'answers' => 'required|array',
    ]);

    SurveyResponse::create([
        'facility_feedback_id' => $id,
        'responder_name' => $request->responder_name,
        'responder_type' => $request->responder_type,
        'answers' => $request->answers,
        'ip_address' => $request->ip(),
    ]);

    $admins = User::role(['Super Admin', 'Staf Operasional'])->get();
    Notification::make()
        ->title('📢 Respon Survei Baru')
        ->body("Seseorang baru saja mengisi form: {$survey->title}")
        ->icon('heroicon-o-document-text')
        ->success()
        ->sendToDatabase($admins);

    return redirect()->back()
        ->with('success', 'Terima kasih atas partisipasi Anda! Jawaban Anda telah tersimpan dengan aman.');
})->name('survey.submit')->middleware('throttle:5,1');
