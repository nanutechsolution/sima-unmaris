<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Kerusakan - {{ $asset->asset_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800 antialiased font-sans">
    <div class="max-w-md mx-auto bg-white min-h-screen shadow-2xl flex flex-col">

        <!-- Header -->
        <div class="bg-red-700 text-white p-5 flex items-center gap-4 shadow-md">
            <a href="{{ route('asset.verify', $asset->qr_signature_hash) }}" class="p-2 bg-red-800 rounded-full hover:bg-red-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="font-bold text-lg leading-tight">Lapor Kerusakan</h1>
                <p class="text-red-200 text-xs">{{ $asset->asset_code }}</p>
            </div>
        </div>

        <!-- Form Konten -->
        <div class="p-6 flex-1">
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-xl mb-6">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Objek Laporan:</p>
                <p class="font-bold text-gray-800">{{ $asset->name }}</p>
                <p class="text-sm text-gray-600 mt-1">Lokasi: {{ $asset->room->name ?? 'Tidak diketahui' }}</p>
            </div>

            <form action="{{ route('asset.report.submit', $asset->qr_signature_hash) }}" method="POST" class="space-y-5">
                @csrf

                <div class="hidden" aria-hidden="true">
                    <label for="website_url">Kosongkan kolom ini jika Anda manusia</label>
                    <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
                </div>

                <div>
                    <label for="problem_description" class="block text-sm font-semibold text-gray-700 mb-2">Jelaskan Kendala / Kerusakan <span class="text-red-500">*</span></label>
                    <textarea
                        name="problem_description"
                        id="problem_description"
                        rows="4"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                        placeholder="Contoh: Layar bergaris, proyektor mati total, tinta printer habis..."
                        required></textarea>
                </div>

                <div>
                    <label for="reporter_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Anda (Opsional)</label>
                    <input
                        type="text"
                        name="reporter_name"
                        id="reporter_name"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                        placeholder="Contoh: Budi (Mhs Sistem Informasi)">
                    <p class="text-xs text-gray-500 mt-2">Boleh dikosongkan jika ingin melapor secara anonim.</p>
                </div>

                <div class="pt-4 mt-8 border-t border-gray-100">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-red-200 transition-all flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Kirim Laporan Kerusakan
                    </button>

                    <p class="text-[11px] text-center text-gray-400 mt-4 px-2 leading-relaxed">
                        Demi menjaga keamanan, sistem mencatat alamat IP perangkat Anda (<span class="font-mono text-gray-500">{{ request()->ip() }}</span>) saat mengirim laporan ini.
                    </p>
                </div>
            </form>
        </div>

    </div>
</body>

</html>