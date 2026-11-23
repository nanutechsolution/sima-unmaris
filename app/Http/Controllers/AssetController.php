<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Halaman publik: Form Cek Status Aset berdasarkan Kode Aset.
     * Jika parameter ?code= diisi, langsung cari dan tampilkan hasilnya.
     */
    public function create(Request $request)
    {
        $asset = null;
        $error = null;

        if ($request->filled('code')) {
            $asset = Asset::with(['category', 'room.location', 'pic'])
                ->where('asset_code', $request->query('code'))
                ->first();

            if (! $asset) {
                $error = 'Aset dengan kode tersebut tidak ditemukan di database kampus.';
            }
        }

        return view('frontend.check-asset', [
            'asset' => $asset,
            'error' => $error,
        ]);
    }

    /**
     * Halaman publik: Detail Aset hasil pencarian berdasarkan kode unik.
     */
    public function show(string $code)
    {
        $asset = Asset::with(['category', 'room.location', 'pic'])
            ->where('asset_code', $code)
            ->firstOrFail();

        return view('frontend.check-asset', [
            'asset' => $asset,
            'error' => null,
        ]);
    }
}
