<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Aset - UNMARIS</title>
    <!-- Menggunakan Tailwind CSS CDN untuk kepraktisan halaman publik eksternal -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- logo -->
    <link rel="icon" href="{{ asset('images/logo-unmaris.png') }}" type="image/x-icon">
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">
    <!-- Kontainer utama menyerupai layar HP -->
    <div class="max-w-md mx-auto bg-white min-h-screen shadow-2xl relative">
        
        <!-- Header Kampus -->
        <div class="bg-blue-800 text-white p-6 text-center shadow-md relative z-10">
            <h1 class="text-3xl font-extrabold tracking-widest uppercase">Unmaris</h1>
            <p class="text-blue-200 text-sm mt-1 font-medium tracking-wide">Sistem Informasi Manajemen Aset</p>
        </div>
        
        <!-- Dekorasi Lengkung -->
        <div class="w-full h-8 bg-blue-800 rounded-b-[50%] absolute z-0 -mt-4"></div>

        <!-- Konten Utama -->
        <div class="p-6 pt-10 relative z-10">
            
            <!-- Alert Sukses (Akan muncul setelah user berhasil melapor) -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 p-4 rounded-xl flex items-start gap-3 shadow-sm mb-6">
                    <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Banner Status Terverifikasi -->
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-2xl flex items-center gap-4 shadow-sm mb-8">
                <div class="bg-green-100 p-2 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-lg leading-tight">Aset Terverifikasi</h2>
                    <p class="text-xs text-green-600 mt-0.5">Resmi terdaftar di database kampus.</p>
                </div>
            </div>

            <!-- Kartu Detail Aset -->
            <div class="bg-white border border-gray-100 rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] p-6 space-y-5">
                
                <!-- Identitas -->
                <div class="text-center pb-4 border-b border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Kode Identitas Aset</p>
                    <p class="font-mono font-bold text-2xl text-blue-900 tracking-wider">{{ $asset->asset_code }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Nama Spesifik Aset</p>
                    <p class="font-semibold text-gray-800 text-lg leading-snug">{{ $asset->name }}</p>
                </div>
                
                <!-- Grid Informasi -->
                <div class="grid grid-cols-2 gap-5 pt-3 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Kategori</p>
                        <p class="text-sm font-medium text-gray-700">{{ $asset->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Status Lifecycle</p>
                        <p class="text-sm font-medium text-blue-600">{{ $asset->status->getLabel() }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5 pt-3 border-t border-gray-100">
                    <div class="col-span-2">
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Kondisi Fisik Saat Ini</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide
                            @if($asset->condition->value === 'good') bg-green-100 text-green-700
                            @elseif($asset->condition->value === 'fair') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $asset->condition->getLabel() }}
                        </span>
                    </div>
                </div>

                <!-- Lokasi & PIC -->
                <div class="pt-3 border-t border-gray-100 bg-gray-50 -mx-6 px-6 py-4 rounded-b-2xl">
                    <div class="mb-4">
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Penempatan
                        </p>
                        <p class="text-sm font-bold text-gray-800">{{ $asset->room->location->name ?? 'Belum ditentukan' }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $asset->room->name ?? '' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Penanggung Jawab (PIC)
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-200 flex items-center justify-center text-blue-800 font-bold shadow-inner">
                                {{ strtoupper(substr($asset->pic->name ?? 'U', 0, 1)) }}
                            </div>
                            <p class="text-sm font-semibold text-gray-800">{{ $asset->pic->name ?? 'Belum ada PIC' }}</p>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL CROWDSOURCING LAPOR KERUSAKAN -->
                <div class="pt-4">
                    <a href="{{ route('asset.report', $asset->qr_signature_hash) }}" class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 py-3 px-4 rounded-xl font-bold transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Lapor Kendala / Kerusakan
                    </a>
                </div>

            </div>
        </div>

        <!-- Footer Timestamp -->
        <div class="text-center pb-8 pt-2">
            <p class="text-xs font-medium text-gray-400">Pengecekan sistem pada: <br/> <span class="text-gray-500">{{ now()->translatedFormat('d F Y - H:i') }}</span></p>
            <p class="text-xs text-gray-400 mt-3">&copy; {{ date('Y') }} Sistem Aset UNMARIS</p>
        </div>
        
    </div>
</body>
</html>