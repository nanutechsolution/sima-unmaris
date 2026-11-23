<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appSettings->getAppName() }} - {{ $appSettings->getInstitutionName() }}</title>
    <meta name="description" content="{{ $appSettings->getAppName() }}: {{ $appSettings->getAppDescription() }}">
    <!-- Menggunakan Tailwind CSS CDN untuk kepraktisan halaman publik eksternal -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- logo -->
    <link rel="icon" href="{{ $appSettings->getFaviconUrl() }}" type="image/x-icon">
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    {{-- ======================= HEADER ======================= --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ $appSettings->getLogoUrl() }}" alt="{{ $appSettings->getAppName() }}" class="h-10 w-10 object-contain">
                <div>
                    <p class="text-lg font-extrabold text-[#1B2A66] leading-tight tracking-wide">{{ $appSettings->getAppName() }}</p>
                    <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest">{{ $appSettings->getInstitutionName() }}</p>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-gray-600">
                <a href="{{ route('home') }}" class="hover:text-[#1B2A66] transition-colors">Beranda</a>
                <a href="#alur-verifikasi" class="hover:text-[#1B2A66] transition-colors">Alur Verifikasi</a>
                <a href="#fitur" class="hover:text-[#1B2A66] transition-colors">Fitur</a>
                <a href="#layanan" class="hover:text-[#1B2A66] transition-colors">Layanan</a>
            </nav>

            <a href="{{ url('/admin/login') }}" class="inline-flex items-center justify-center gap-2 bg-[#1B2A66] hover:bg-blue-900 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-blue-900/20 transition-colors whitespace-nowrap">
                Portal Kelas &amp; Staf
            </a>
        </div>
    </header>

    {{-- ======================= HERO ======================= --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-[#1B2A66] via-[#141C4E] to-[#0C1330] text-white">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#FACC15]/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-24 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-6xl mx-auto px-4 py-16 md:py-24 grid lg:grid-cols-2 gap-14 items-center">
            <div>
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-[#FACC15] text-xs font-bold uppercase tracking-widest">
                    🏛️ {{ $appSettings->getInstitutionName() }}
                </span>
                <h1 class="mt-5 text-4xl md:text-5xl font-extrabold tracking-tight leading-tight text-white">
                    {{ $appSettings->getAppName() }}
                </h1>
                <p class="mt-5 text-base md:text-lg text-blue-100 leading-relaxed">
                    {{ $appSettings->getAppName() }} menghubungkan inventaris aset kampus, verifikasi identitas digital melalui QR Code, pelaporan kerusakan, dan survei kepuasan civitas akademika dalam satu platform terpadu.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('asset.check') }}" class="inline-flex items-center justify-center gap-2 bg-[#FACC15] hover:bg-yellow-400 text-[#1B2A66] font-bold px-7 py-3.5 rounded-xl shadow-lg shadow-[#FACC15]/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6M9 11h4"></path></svg>
                        Cek Status Aset
                    </a>
                    <a href="{{ url('/admin/login') }}" class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold px-7 py-3.5 rounded-xl transition-colors">
                        Portal Kelas &amp; Staf
                    </a>
                </div>
                <p class="mt-6 text-xs text-blue-200">Dikembangkan oleh Biro Sarpras Universitas Stella Maris</p>
            </div>

            {{-- Kartu Visual Status Aset --}}
            <div class="relative mx-auto w-full max-w-sm">
                <div class="absolute -inset-8 bg-[#FACC15]/20 blur-3xl rounded-full"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl p-6 text-gray-800">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status Verifikasi</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-[11px] font-bold">Terverifikasi</span>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kode Identitas Aset</p>
                        <p class="font-mono font-bold text-2xl text-[#1B2A66] tracking-wider mt-1">UNMARIS/IT/2026/001</p>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-sm font-semibold text-gray-800">Laptop Bisnis Enterprise</p>
                        <p class="text-xs text-gray-500 mt-0.5">Laboratorium Komputer A</p>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 pt-4 border-t border-gray-100">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kondisi</p>
                            <p class="text-xs font-bold text-green-600 mt-1">Baik</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kategori</p>
                            <p class="text-xs font-bold text-[#1B2A66] mt-1">Komputer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= STATISTIK ======================= --}}
    <section class="max-w-6xl mx-auto px-4 -mt-10 relative z-10 mb-4">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 grid grid-cols-2 lg:grid-cols-4 gap-px overflow-hidden rounded-3xl">
            <div class="p-6 text-center">
                <p class="text-3xl font-extrabold text-[#1B2A66]">{{ $stats['total_assets'] }}</p>
                <p class="mt-1 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Aset Terdaftar</p>
            </div>
            <div class="p-6 text-center">
                <p class="text-2xl font-extrabold text-[#1B2A66]">Rp {{ number_format($stats['inventory_value'], 0, ',', '.') }}</p>
                <p class="mt-1 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Nilai Inventaris (Rp)</p>
            </div>
            <div class="p-6 text-center">
                <p class="text-3xl font-extrabold text-[#1B2A66]">{{ $stats['categories'] }}</p>
                <p class="mt-1 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Kategori Aset</p>
            </div>
            <div class="p-6 text-center">
                <p class="text-3xl font-extrabold text-[#1B2A66]">{{ $stats['rooms'] }}</p>
                <p class="mt-1 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Ruang Terdaftar</p>
            </div>
        </div>
    </section>

    {{-- ======================= ALUR VERIFIKASI ======================= --}}
    <section id="alur-verifikasi" class="max-w-6xl mx-auto px-4 py-20 scroll-mt-24">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold text-[#F59E0B] uppercase tracking-widest">Cara Kerja</span>
            <h2 class="mt-2 text-3xl font-extrabold text-gray-900">Verifikasi Aset dalam 3 Langkah</h2>
            <p class="mt-3 text-gray-600">Tanpa perlu login, siapa pun dapat memverifikasi kebenaran sebuah aset melalui proses digital signature yang ketat.</p>
        </div>

        <div class="mt-14 grid md:grid-cols-3 gap-6">
            {{-- Step 1 --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <div class="w-14 h-14 rounded-full bg-[#1B2A66] text-white font-extrabold text-xl flex items-center justify-center mx-auto">1</div>
                <div class="mt-5">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-[#EEF2FF] flex items-center justify-center text-[#1B2A66]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4V2m0 6l-3 3m3-3l3 3m0 6l-3 3m3-3l3 3M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path></svg>
                    </div>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Scan QR Code</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Gunakan smartphone untuk memindai stiker QR pada aset fisik yang ingin Anda verifikasi.</p>
            </div>

            {{-- Step 2 --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <div class="w-14 h-14 rounded-full bg-[#1B2A66] text-white font-extrabold text-xl flex items-center justify-center mx-auto">2</div>
                <div class="mt-5">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-[#EEF2FF] flex items-center justify-center text-[#1B2A66]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Terverifikasi Resmi</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Sistem memvalidasi digital signature (HMAC-SHA256) dan mengirimkannya ke basis data inventaris kampus.</p>
            </div>

            {{-- Step 3 --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <div class="w-14 h-14 rounded-full bg-[#1B2A66] text-white font-extrabold text-xl flex items-center justify-center mx-auto">3</div>
                <div class="mt-5">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-[#EEF2FF] flex items-center justify-center text-[#1B2A66]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Lihat Status &amp; Lapor</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Pantau kondisi fisik aset atau ajukan laporan kerusakan melalui formulir publik yang dilindungi sistem.</p>
            </div>
        </div>
    </section>

    {{-- ======================= FITUR ======================= --}}
    <section id="fitur" class="max-w-6xl mx-auto px-4 py-20 scroll-mt-24">
        <div class="text-center max-w-2xl mx-auto">
            <span class="text-xs font-bold text-[#F59E0B] uppercase tracking-widest">Keunggulan</span>
            <h2 class="mt-2 text-3xl font-extrabold text-gray-900">Fitur Unggulan {{ $appSettings->getAppName() }}</h2>
            <p class="mt-3 text-gray-600">Satu ekosistem lengkap untuk menjawab seluruh kebutuhan operasional dan layanan kampus.</p>
        </div>

        <div class="mt-14 grid md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6">
                <div class="w-12 h-12 rounded-xl bg-[#1B2A66]/10 flex items-center justify-center text-[#1B2A66]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7h-9.01M20 4h.01M12 7H7.01M12 4H7.01M4 9H20"></path></svg>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Manajemen Inventaris</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Pendataan terpusat seluruh aset kampus dengan data lifecycle, lokasi penempatan, PIC, dan nilai penyusutan.</p>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6">
                <div class="w-12 h-12 rounded-xl bg-[#1B2A66]/10 flex items-center justify-center text-[#1B2A66]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8V4m0 4l-4 4m4-4l4 4m0 8l-4 4m4-4v8"></path></svg>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Verifikasi Digital QR</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Setiap aset diberi digital signature unik (HMAC-SHA256) yang mengarah ke halaman verifikasi resmi.</p>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6">
                <div class="w-12 h-12 rounded-xl bg-[#1B2A66]/10 flex items-center justify-center text-[#1B2A66]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Laporan Kerusakan</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Pelaporan kerusakan dilakukan secara crowdsourced dengan perlindungan honeypot dan throttling rate limit.</p>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6">
                <div class="w-12 h-12 rounded-xl bg-[#1B2A66]/10 flex items-center justify-center text-[#1B2A66]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14l-1.5-2A2 2 0 0116.5 5H7.5a2 2 0 01-1.5 1L5 8z"></path></svg>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Inventori &amp; ATK</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Kelola alat tulis dan perlengkapan inventaris dengan sistem transaksi stok yang terdokumentasi.</p>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6">
                <div class="w-12 h-12 rounded-xl bg-[#1B2A66]/10 flex items-center justify-center text-[#1B2A66]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Survei Kepuasan</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Template survei dinamis (rating, pilihan, teks) untuk mengukur kepuasan layanan kampus secara berkala.</p>
            </div>

            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-6">
                <div class="w-12 h-12 rounded-xl bg-[#1B2A66]/10 flex items-center justify-center text-[#1B2A66]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="mt-4 font-bold text-gray-800 text-lg">Audit &amp; Laporan</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">Ekspor laporan akreditasi (PDF/Excel) dan jejak audit aktivitas sistem yang transparan.</p>
            </div>
        </div>
    </section>

    {{-- ======================= CTA / LAYANAN ======================= --}}
    <section id="layanan" class="max-w-6xl mx-auto px-4 py-20 scroll-mt-24">
        <div class="bg-gradient-to-br from-[#1B2A66] to-[#0C1330] rounded-[2rem] px-6 py-14 text-center text-white relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-[#FACC15]/10 rounded-full blur-3xl"></div>
            <div class="relative max-w-2xl mx-auto">
                <h2 class="text-3xl font-extrabold">Butuh Membuktikan Status Aset Hari Ini?</h2>
                <p class="mt-4 text-blue-100 leading-relaxed">Scan QR Code pada stiker aset fisik atau akses portal untuk staf dan administrator kampus.</p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('asset.check') }}" class="inline-flex items-center justify-center gap-2 bg-[#FACC15] hover:bg-yellow-400 text-[#1B2A66] font-bold px-7 py-3.5 rounded-xl shadow-lg shadow-[#FACC15]/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6M9 11h4"></path></svg>
                        Cek Status Aset
                    </a>
                    <a href="{{ url('/admin/login') }}" class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold px-7 py-3.5 rounded-xl transition-colors">
                        Portal Kelas &amp; Staf
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= FOOTER ======================= --}}
    <footer class="bg-[#0C1330] text-gray-400">
        <div class="max-w-6xl mx-auto px-4 py-12 grid md:grid-cols-3 gap-10">
            <div>
                <div class="flex items-center gap-3">
                    <img src="{{ $appSettings->getLogoUrl() }}" alt="{{ $appSettings->getAppName() }}" class="h-10 w-10 object-contain">
                    <div>
                        <p class="text-lg font-extrabold text-white leading-tight tracking-wide">{{ $appSettings->getAppName() }}</p>
                        <p class="text-[11px] font-semibold uppercase tracking-widest">{{ $appSettings->getInstitutionName() }}</p>
                    </div>
                </div>
                <p class="mt-4 text-sm leading-relaxed">Ekosistem digital terpadu untuk pengelolaan inventaris aset, verifikasi identitas, pelaporan kerusakan, dan survei kepuasan di lingkungan Universitas Stella Maris.</p>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">Tautan Cepat</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                    <li><a href="#alur-verifikasi" class="hover:text-white transition-colors">Alur Verifikasi Aset</a></li>
                    <li><a href="#fitur" class="hover:text-white transition-colors">Fitur SIMA</a></li>
                    <li><a href="#layanan" class="hover:text-white transition-colors">Layanan Publik</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-bold mb-4">Kontak</h3>
                <ul class="space-y-2 text-sm">
                    <li>Jl. Karya Kasih No. 5, Tambolaka</li>
                    <li>Kab. Sumba Barat Daya</li>
                    <li>Email: <a href="mailto:rektorat@unmaris.ac.id" class="hover:text-white transition-colors">rektorat@unmaris.ac.id</a></li>
                    <li>Telp: (021) 1234567</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="max-w-6xl mx-auto px-4 py-5 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} {{ $appSettings->getAppName() }} - Biro Sarpras {{ $appSettings->getInstitutionName() }}
            </div>
        </div>
    </footer>

</body>
</html>
