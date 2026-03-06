<?php

use App\Models\Asset;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/verify-asset/{signature}', function (string $signature) {
    // Cari aset berdasarkan digital signature yang terenkripsi di QR
    $asset = Asset::with(['category', 'room.location', 'pic'])
        ->where('qr_signature_hash', $signature)
        ->firstOrFail();

    // Mengembalikan tampilan halaman publik (Frontend) dengan membawa data $asset
    return view('frontend.asset-verify', compact('asset'));
})->name('asset.verify');