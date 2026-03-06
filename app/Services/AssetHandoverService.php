<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetHandover;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class AssetHandoverService
{
    /**
     * Memproses serah terima aset dengan jaminan Database Transaction.
     */
    public function processHandover(
        Asset $asset,
        string $receiverUserId,
        string $locationId,
        string $assetPhotoPath,     // Ubah menjadi string
        string $documentPhotoPath,  // Ubah menjadi string
        ?string $notes = null
    ): AssetHandover {
        return DB::transaction(function () use (
            $asset,
            $receiverUserId,
            $locationId,
            $assetPhotoPath,
            $documentPhotoPath,
            $notes
        ) {
            try {
                // 1. Catat Histori Penyerahan
                $handover = AssetHandover::create([
                    'asset_id' => $asset->id,
                    'giver_user_id' => auth()->id() ?? $asset->pic_user_id,
                    'receiver_user_id' => $receiverUserId,
                    'location_id' => $locationId,
                    'handover_time' => now(),
                    'asset_photo_path' => $assetPhotoPath,       // Langsung simpan path dari Filament
                    'document_photo_path' => $documentPhotoPath, // Langsung simpan path dari Filament
                    'notes' => $notes,
                    'digital_signature_hash' => hash('sha256', $asset->id . $receiverUserId . now()->timestamp),
                ]);

                // 2. Update Status dan Penanggung Jawab di Master Asset
                $asset->update([
                    'pic_user_id' => $receiverUserId,
                    'status' => \App\Enums\AssetStatusEnum::IN_USE,
                ]);

                return $handover;

            } catch (Exception $e) {
                Log::error('Asset Handover Failed: ' . $e->getMessage(), [
                    'asset_id' => $asset->id,
                    'receiver_id' => $receiverUserId,
                ]);
                throw $e;
            }
        });
    }
}