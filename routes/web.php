<?php

use App\Models\Asset;
use App\Models\MaintenanceLog;
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

