@extends('frontend.layouts.public')

@section('title', 'Cek Status Aset — '.$appSettings->getAppName())
@section('meta_description', 'Periksa status resmi aset '.$appSettings->getInstitutionName().' berdasarkan kode identitas aset atau pindai QR pada aset.')

@section('content')
    <div class="relative overflow-hidden border-b border-slate-200/70 bg-white">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-brand-50/70 to-transparent" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-brand-700">Beranda</a>
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-600" aria-current="page">Cek Status Aset</span>
            </nav>

            <div class="mt-5 flex items-start gap-4">
                <span class="hidden h-14 w-14 flex-none place-items-center rounded-2xl bg-brand-800 text-white shadow-lg shadow-brand-800/20 sm:grid">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                </span>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Cek Status Aset</h1>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600">
                        Masukkan kode identitas aset untuk melihat data resmi inventaris {{ $appSettings->getInstitutionName() }}.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        @if($asset)
            {{-- ==================== HASIL DITEMUKAN ==================== --}}
            <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800" role="status">
                <span class="grid h-9 w-9 flex-none place-items-center rounded-full bg-emerald-100 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h2 class="text-sm font-extrabold">Aset terverifikasi</h2>
                    <p class="mt-0.5 text-xs leading-relaxed text-emerald-700">Aset ini resmi terdaftar di database inventaris kampus.</p>
                </div>
            </div>

            @include('frontend.partials.asset-card', ['asset' => $asset])

            <div class="mt-5 flex flex-col items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-5 py-4 text-center text-xs text-slate-500 shadow-card sm:flex-row sm:text-left">
                <p>
                    Pengecekan sistem pada
                    <span class="font-semibold text-slate-700">{{ now()->translatedFormat('d F Y - H:i') }}</span>
                </p>
                <a href="{{ route('asset.check') }}" class="inline-flex items-center gap-1.5 font-bold text-brand-700 transition hover:text-brand-900">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                    Cek aset lain
                </a>
            </div>
        @else
            {{-- ==================== FORM PENCARIAN ==================== --}}
            @if($error)
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800" role="alert">
                    <svg class="mt-0.5 h-5 w-5 flex-none text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="text-sm font-extrabold">Aset tidak ditemukan</p>
                        <p class="mt-0.5 text-xs leading-relaxed text-rose-700">{{ $error }}</p>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-soft">
                <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4 sm:px-7">
                    <h2 class="text-sm font-extrabold uppercase tracking-[0.14em] text-slate-700">Kode Identitas Aset</h2>
                    <p class="mt-1 text-xs text-slate-500">Kode dapat ditemukan pada stiker QR atau dokumen inventaris unit.</p>
                </div>

                <form action="{{ route('asset.check') }}" method="GET" class="space-y-5 px-5 py-6 sm:px-7 sm:py-7">
                    <div>
                        <label for="code" class="mb-1.5 block text-xs font-bold text-slate-500">Masukkan kode aset <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </span>
                            <input
                                type="text"
                                name="code"
                                id="code"
                                value="{{ request()->query('code') }}"
                                required
                                autocomplete="off"
                                spellcheck="false"
                                placeholder="Contoh: UNMARIS/IT/2026/001"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3.5 pl-11 pr-4 font-mono text-sm text-slate-900 outline-none transition placeholder:font-sans placeholder:text-slate-400 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-600/30"
                            >
                        </div>
                        <p class="mt-2 text-[11px] leading-relaxed text-slate-400">Peka terhadap huruf besar/kecil dan tanda garis miring. Salin persis seperti pada stiker.</p>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-800 px-5 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-brand-800/20 transition hover:bg-brand-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                        Cek Status Aset
                    </button>
                </form>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <div class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-card">
                    <span class="grid h-9 w-9 flex-none place-items-center rounded-xl bg-brand-50 text-brand-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800">Punya stiker QR?</h3>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500">Pindai QR pada aset untuk langsung membuka halaman verifikasi.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-card">
                    <span class="grid h-9 w-9 flex-none place-items-center rounded-xl bg-rose-50 text-rose-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800">Menemukan kerusakan?</h3>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500">Buka detail aset lalu pilih <span class="font-semibold text-slate-700">Lapor Kerusakan</span>.</p>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                Pengecekan sistem pada <span class="font-semibold text-slate-500">{{ now()->translatedFormat('d F Y - H:i') }}</span>
            </p>
        @endif
    </div>
@endsection
