<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SurveyController;
use App\Models\Asset;
use App\Models\MaintenanceLog;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// HALAMAN PUBLIK: Cek Status Aset berdasarkan Kode Aset (misal: UNMARIS/IT/2026/001)
Route::get('/cek-aset', [AssetController::class, 'create'])->name('asset.check');
Route::get('/cek-aset/{code}', [AssetController::class, 'show'])->name('asset.check.show');

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
    if (! empty($request->website_url)) {
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
        'problem_description' => '[LAPORAN QR] '.$request->problem_description,
        'status' => 'scheduled',
        // Menyimpan IP Address si pelapor di catatan agar admin tahu
        'notes' => 'Dilaporkan oleh: '.($request->reporter_name ?: 'Anonim').' | IP: '.$request->ip(),
    ]);
    // 3. KIRIM NOTIFIKASI REAL-TIME KE ADMIN & STAF
    // Mengambil semua user yang memiliki role super_admin atau staf_inventaris
    $admins = User::role(['super_admin', 'staf_inventaris'])->get();

    // Tembakkan notifikasi ke database
    Notification::make()
        ->title('🚨 Laporan Kerusakan Baru!')
        ->body("Aset {$asset->asset_code} ({$asset->name}) dilaporkan rusak oleh ".($request->reporter_name ?: 'pengguna anonim').'.')
        ->icon('heroicon-o-exclamation-triangle')
        ->warning()
        ->sendToDatabase($admins);

    return redirect()->route('asset.verify', $signature)
        ->with('success', 'Terima kasih! Laporan kerusakan telah terkirim.');
})->name('asset.report.submit')->middleware('throttle:3,1');

// RUTE HALAMAN SURVEI PUBLIK (Dinamis berdasarkan template dari Database)
Route::get('/surveys/{id}', [SurveyController::class, 'show'])->name('survey.show');
Route::post('/surveys/{id}/submit', [SurveyController::class, 'submit'])->name('survey.submit')->middleware('throttle:5,1');
