<?php

namespace App\Services;

use App\Models\Asset;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrIdentityService
{
    /**
     * Meng-generate QR Code fisik untuk ditempel di Aset.
     * QR Code ini berisi URL terenkripsi/unik menuju halaman profil aset.
     */
    public function generateAssetQrCode(Asset $asset): string
    {
        // 1. Buat Digital Signature Hash jika belum ada
        if (!$asset->qr_signature_hash) {
            // Gunakan kombinasi ID, Kode, dan Salt (app key) agar tidak bisa ditebak
            $signature = hash_hmac('sha256', $asset->id . $asset->asset_code, config('app.key'));
            $asset->update(['qr_signature_hash' => $signature]);
        }

        // 2. Buat URL Verifikasi (Akan mengarah ke halaman frontend/scanner kampus)
        $verifyUrl = route('asset.verify', ['signature' => $asset->qr_signature_hash]);

        // 3. Generate File QR Code (Format SVG agar kualitas tidak pecah saat dicetak stiker)
        $qrContent = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H') // High error correction agar tetap terbaca meski stiker lecet
            ->generate($verifyUrl);

        // 4. Simpan QR ke Storage (Opsional, jika ingin dilampirkan ke PDF/Laporan)
        $fileName = "qr_codes/{$asset->asset_code}.svg";
        Storage::disk('public')->put($fileName, $qrContent);

        return Storage::url($fileName);
    }
}