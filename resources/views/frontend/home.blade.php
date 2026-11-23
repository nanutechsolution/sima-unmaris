@extends('frontend.layouts.public')

@section('title', $appSettings->getAppName().' — Sistem Informasi Manajemen Aset '.$appSettings->getInstitutionName())
@section('meta_description', $appSettings->getAppDescription().'. Verifikasi keaslian aset, cek status inventaris, dan laporkan kerusakan secara daring di '.$appSettings->getInstitutionName().'.')

@php
    $value = (float) $stats['inventory_value'];
    $valueLabel = $value >= 1_000_000_000
        ? 'Rp '.number_format($value / 1_000_000_000, 1, ',', '.').' M'
        : ($value >= 1_000_000
            ? 'Rp '.number_format($value / 1_000_000, 1, ',', '.').' Jt'
            : 'Rp '.number_format($value, 0, ',', '.'));

    $statItems = [
        ['label' => 'Aset Terdata', 'value' => number_format($stats['total_assets'], 0, ',', '.'), 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['label' => 'Nilai Inventaris', 'value' => $valueLabel, 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Kategori Aset', 'value' => number_format($stats['categories'], 0, ',', '.'), 'icon' => 'M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z'],
        ['label' => 'Ruang / Lokasi', 'value' => number_format($stats['rooms'], 0, ',', '.'), 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
    ];
@endphp

@section('content')
    {{-- ======================= HERO ======================= --}}
    <section class="relative overflow-hidden bg-brand-950 text-white">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -left-32 top-0 h-80 w-80 rounded-full bg-brand-600/30 blur-3xl"></div>
            <div class="absolute -right-20 bottom-0 h-96 w-96 rounded-full bg-accent-500/10 blur-3xl"></div>
            <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 44px 44px;"></div>
        </div>

        <div class="relative mx-auto grid max-w-7xl gap-12 px-4 pb-16 pt-12 sm:px-6 sm:pt-16 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-16 lg:px-8 lg:pb-24 lg:pt-20">
            <div class="max-w-2xl">
                <p class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-[0.16em] text-brand-100 backdrop-blur">
                    <span class="relative flex h-2 w-2" aria-hidden="true">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                    </span>
                    Inventaris kampus terverifikasi
                </p>

                <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight sm:text-5xl lg:text-6xl">
                    Aset kampus,
                    <span class="bg-gradient-to-r from-accent-300 to-accent-500 bg-clip-text text-transparent">terverifikasi</span>
                    dalam satu pindai.
                </h1>

                <p class="mt-5 max-w-xl text-base leading-relaxed text-brand-100/85 sm:text-lg">
                    {{ $appSettings->getAppName() }} menghubungkan stiker QR, data inventaris, dan pelaporan kerusakan di lingkungan {{ $appSettings->getInstitutionName() }} — transparan, cepat, dan bisa diakses siapa saja.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('asset.check') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-accent-400 px-6 py-3.5 text-sm font-extrabold text-brand-950 shadow-lg shadow-accent-500/20 transition hover:bg-accent-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-300 focus-visible:ring-offset-2 focus-visible:ring-offset-brand-950">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                        Cek Status Aset
                    </a>
                    <a href="#alur-verifikasi"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/5 px-6 py-3.5 text-sm font-bold text-white backdrop-blur transition hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-950">
                        Lihat Cara Verifikasi
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </a>
                </div>

                <dl class="mt-10 grid grid-cols-3 gap-4 border-t border-white/10 pt-6 text-left">
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-brand-300">Aset</dt>
                        <dd class="mt-1 text-2xl font-extrabold">{{ number_format($stats['total_assets'], 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-brand-300">Ruang</dt>
                        <dd class="mt-1 text-2xl font-extrabold">{{ number_format($stats['rooms'], 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold uppercase tracking-[0.14em] text-brand-300">Survei Aktif</dt>
                        <dd class="mt-1 text-2xl font-extrabold">{{ number_format($stats['active_surveys'], 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Kartu ilustrasi verifikasi --}}
            <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-tr from-accent-400/20 via-transparent to-white/10 blur-xl" aria-hidden="true"></div>
                <div class="relative rounded-3xl border border-white/15 bg-white/10 p-2 shadow-soft backdrop-blur-xl">
                    <div class="rounded-[1.35rem] bg-white p-5 text-slate-800 sm:p-6">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                Terverifikasi
                            </span>
                            <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-slate-400">SIMA · QR</span>
                        </div>

                        <div class="mt-5 flex items-center gap-4">
                            <div class="grid h-16 w-16 flex-none place-items-center rounded-2xl bg-brand-950 p-2" aria-hidden="true">
                                <div class="grid grid-cols-4 gap-0.5">
                                    @for($i = 0; $i < 16; $i++)
                                        <span class="h-1.5 w-1.5 rounded-[1px] {{ in_array($i, [0,1,2,3,4,6,7,8,11,12,13,14,15]) ? 'bg-white' : 'bg-transparent' }}"></span>
                                    @endfor
                                </div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Kode Aset</p>
                                <p class="truncate font-mono text-sm font-extrabold text-brand-900">UNMARIS/IT/2026/001</p>
                                <p class="mt-1 truncate text-xs text-slate-500">Laptop Lenovo ThinkPad L14</p>
                            </div>
                        </div>

                        <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-slate-50 px-3 py-2.5">
                                <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Kondisi</dt>
                                <dd class="mt-0.5 font-bold text-emerald-600">Baik</dd>
                            </div>
                            <div class="rounded-xl bg-slate-50 px-3 py-2.5">
                                <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Status</dt>
                                <dd class="mt-0.5 font-bold text-sky-600">Digunakan</dd>
                            </div>
                            <div class="col-span-2 rounded-xl bg-slate-50 px-3 py-2.5">
                                <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Penempatan</dt>
                                <dd class="mt-0.5 text-xs font-semibold text-slate-700">Lab Komputer · Gedung Rektorat Lt. 2</dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex items-center gap-2 rounded-xl border border-dashed border-slate-200 px-3 py-2.5 text-[11px] font-semibold text-slate-500">
                            <svg class="h-4 w-4 flex-none text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.6 1.4A9 9 0 1112 3a9 9 0 018.6 6.4z"/></svg>
                            Data resmi dari database inventaris kampus
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-5 -left-4 hidden items-center gap-2 rounded-2xl border border-white/15 bg-white/95 px-3.5 py-2.5 text-slate-800 shadow-soft sm:flex" aria-hidden="true">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-rose-50 text-rose-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    <span class="text-xs font-bold leading-tight">Lapor kerusakan<br><span class="font-medium text-slate-500">langsung dari ponsel</span></span>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= STATISTIK ======================= --}}
    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="Statistik inventaris">
        <div class="grid grid-cols-2 gap-3 rounded-3xl border border-slate-200/80 bg-white p-4 shadow-soft sm:gap-4 sm:p-6 lg:grid-cols-4">
            @foreach($statItems as $item)
                <div class="rounded-2xl bg-slate-50/80 p-4 sm:p-5">
                    <div class="flex items-center gap-2.5">
                        <span class="grid h-9 w-9 flex-none place-items-center rounded-xl bg-brand-100 text-brand-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                        </span>
                        <p class="text-[10px] font-bold uppercase leading-tight tracking-[0.14em] text-slate-400">{{ $item['label'] }}</p>
                    </div>
                    <p class="mt-3 text-xl font-extrabold tracking-tight text-brand-900 sm:text-2xl">{{ $item['value'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ======================= ALUR ======================= --}}
    <section id="alur-verifikasi" class="scroll-mt-24 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-brand-600">Alur verifikasi</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Tiga langkah, aset langsung terverifikasi</h2>
                <p class="mt-4 text-base leading-relaxed text-slate-600">Tidak perlu login. Semua orang dapat memverifikasi keaslian aset dan melaporkan kendala secara langsung.</p>
            </div>

            <ol class="mt-12 grid gap-5 md:grid-cols-3">
                @php
                    $steps = [
                        ['n' => '01', 'title' => 'Pindai atau masukkan kode', 'desc' => 'Arahkan kamera ke stiker QR pada aset, atau ketik kode identitas aset pada halaman cek aset.', 'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z'],
                        ['n' => '02', 'title' => 'Periksa data resmi', 'desc' => 'Sistem menampilkan nama aset, kategori, kondisi, status, lokasi, dan penanggung jawab langsung dari database.', 'icon' => 'M9 12l2 2 4-4m5.6 1.4A9 9 0 1112 3a9 9 0 018.6 6.4z'],
                        ['n' => '03', 'title' => 'Laporkan bila bermasalah', 'desc' => 'Menemukan kerusakan? Kirim laporan dari halaman aset — tanpa aplikasi tambahan, cukup beberapa detik.', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    ];
                @endphp

                @foreach($steps as $step)
                    <li class="group relative rounded-3xl border border-slate-200/80 bg-white p-6 shadow-card transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-soft sm:p-7">
                        <div class="flex items-center justify-between">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-700 transition group-hover:bg-brand-800 group-hover:text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $step['icon'] }}"/></svg>
                            </span>
                            <span class="font-mono text-3xl font-extrabold text-slate-100 transition group-hover:text-brand-100" aria-hidden="true">{{ $step['n'] }}</span>
                        </div>
                        <h3 class="mt-5 text-lg font-extrabold text-slate-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $step['desc'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ======================= FITUR ======================= --}}
    <section id="fitur" class="scroll-mt-24 border-y border-slate-200/70 bg-white py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start lg:gap-16">
                <div class="lg:sticky lg:top-28">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-brand-600">Fitur utama</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Dirancang untuk transparansi pengelolaan aset</h2>
                    <p class="mt-4 text-base leading-relaxed text-slate-600">
                        Dari pencatatan hingga pelaporan, setiap fitur dibangun agar data inventaris kampus mudah diakses dan sulit disalahgunakan.
                    </p>
                    <a href="{{ route('asset.check') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-extrabold text-brand-700 transition hover:text-brand-900">
                        Mulai cek aset sekarang
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>

                @php
                    $features = [
                        ['title' => 'Verifikasi QR asli', 'desc' => 'Setiap aset memiliki tanda tangan digital unik sehingga stiker palsu mudah dikenali.', 'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z'],
                        ['title' => 'Data kondisi real-time', 'desc' => 'Kondisi fisik dan status pemakaian aset selalu mengikuti pencatatan terbaru dari unit pengelola.', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        ['title' => 'Pelaporan tanpa aplikasi', 'desc' => 'Mahasiswa, dosen, dan staf dapat melapor kerusakan langsung dari browser ponsel.', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                        ['title' => 'Riwayat pemeliharaan', 'desc' => 'Setiap laporan masuk ke catatan pemeliharaan aset agar penanganan tidak terlewat.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['title' => 'Survei fasilitas', 'desc' => 'Kumpulkan umpan balik layanan kampus melalui survei dinamis yang dapat diisi anonim.', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-6 4h6m-6-4h.01'],
                        ['title' => 'Branding institusi', 'desc' => 'Identitas visual mengikuti pengaturan resmi institusi, konsisten di seluruh halaman publik.', 'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
                    ];
                @endphp

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($features as $feature)
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5 transition hover:border-brand-200 hover:bg-white hover:shadow-card">
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-white text-brand-700 shadow-sm ring-1 ring-slate-200/70">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $feature['icon'] }}"/></svg>
                            </span>
                            <h3 class="mt-3.5 text-sm font-extrabold text-slate-900">{{ $feature['title'] }}</h3>
                            <p class="mt-1.5 text-[13px] leading-relaxed text-slate-600">{{ $feature['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= FAQ ======================= --}}
    <section id="faq" class="scroll-mt-24 py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-[11px] font-extrabold uppercase tracking-[0.2em] text-brand-600">Pertanyaan umum</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Hal yang sering ditanyakan</h2>
            </div>

            @php
                $faqs = [
                    ['q' => 'Bagaimana cara mengetahui kode aset saya?', 'a' => 'Kode aset tercetak pada stiker QR yang menempel di badan aset, dan juga tercantum pada dokumen inventaris unit Anda. Formatnya seperti UNMARIS/IT/2026/001.'],
                    ['q' => 'Apakah data aset bisa diakses publik?', 'a' => 'Halaman publik hanya menampilkan informasi identitas aset yang diperlukan untuk verifikasi: nama, kategori, kondisi, status, lokasi, dan penanggung jawab. Data sensitif tetap berada di panel administrasi.'],
                    ['q' => 'Siapa yang dapat mengirim laporan kerusakan?', 'a' => 'Siapa saja — mahasiswa, dosen, staf, maupun tamu. Laporan dapat dikirim secara anonim, dan sistem mencatat alamat IP untuk mencegah penyalahgunaan.'],
                    ['q' => 'Apakah saya perlu membuat akun?', 'a' => 'Tidak. Seluruh layanan publik seperti cek aset, verifikasi QR, pelaporan kerusakan, dan pengisian survei dapat digunakan tanpa login.'],
                    ['q' => 'Mengapa saya tidak bisa mengisi survei dua kali?', 'a' => 'Untuk menjaga validitas data, sistem membatasi satu pengisian per alamat IP dan menyimpan penanda pada browser Anda.'],
                ];
            @endphp

            <div class="mt-10 space-y-3">
                @foreach($faqs as $faq)
                    <details class="group rounded-2xl border border-slate-200/80 bg-white px-5 py-4 shadow-card open:border-brand-200 sm:px-6">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-left text-sm font-extrabold text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                            {{ $faq['q'] }}
                            <span class="grid h-7 w-7 flex-none place-items-center rounded-full bg-slate-100 text-slate-500 transition group-open:rotate-45 group-open:bg-brand-800 group-open:text-white" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </span>
                        </summary>
                        <p class="mt-3 pr-11 text-sm leading-relaxed text-slate-600">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======================= CTA ======================= --}}
    <section class="pb-16 sm:pb-20 lg:pb-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-brand-900 px-6 py-12 text-center shadow-soft sm:px-12 sm:py-16">
                <div class="pointer-events-none absolute -left-16 -top-16 h-64 w-64 rounded-full bg-accent-400/10 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-24 -right-10 h-72 w-72 rounded-full bg-brand-500/30 blur-3xl" aria-hidden="true"></div>
                <div class="relative mx-auto max-w-2xl">
                    <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Pastikan aset kampus dalam kondisi terbaik</h2>
                    <p class="mt-4 text-base leading-relaxed text-brand-100/85">Satu laporan Anda membantu tim Sarpras menindaklanjuti lebih cepat. Mulai dari memeriksa kode aset hari ini.</p>
                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                        <a href="{{ route('asset.check') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-accent-400 px-6 py-3.5 text-sm font-extrabold text-brand-950 shadow-lg shadow-accent-500/20 transition hover:bg-accent-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-300 focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                            Cek Status Aset
                        </a>
                        <a href="{{ url('/admin/login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/5 px-6 py-3.5 text-sm font-bold text-white transition hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900">
                            Masuk Portal Staf
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
