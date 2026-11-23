@extends('frontend.layouts.public')

@section('title', 'Verifikasi Aset — '.$appSettings->getAppName())
@section('meta_description', 'Halaman verifikasi keaslian aset '.$appSettings->getInstitutionName().' berdasarkan tanda tangan digital pada stiker QR.')

@section('content')
    <div class="relative overflow-hidden bg-brand-950 text-white">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -left-24 top-0 h-64 w-64 rounded-full bg-brand-600/30 blur-3xl"></div>
            <div class="absolute -right-16 bottom-0 h-72 w-72 rounded-full bg-accent-500/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-3xl px-4 pb-16 pt-10 sm:px-6 sm:pb-20 sm:pt-14 lg:px-8">
            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3.5 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.16em] text-emerald-300 backdrop-blur">
                <span class="grid h-4 w-4 place-items-center rounded-full bg-emerald-400 text-brand-950" aria-hidden="true">
                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </span>
                Hasil verifikasi QR
            </div>

            <h1 class="mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">Aset terverifikasi resmi</h1>
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-brand-100/80 sm:text-base">
                Tanda tangan digital pada QR cocok dengan catatan inventaris {{ $appSettings->getInstitutionName() }}. Berikut detail aset yang Anda pindai.
            </p>
        </div>
    </div>

    <div class="mx-auto -mt-10 max-w-3xl px-4 pb-12 sm:-mt-12 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-card" role="status">
                <span class="grid h-9 w-9 flex-none place-items-center rounded-full bg-emerald-100 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-extrabold">Laporan terkirim</p>
                    <p class="mt-0.5 text-xs leading-relaxed text-emerald-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @include('frontend.partials.asset-card', ['asset' => $asset])

        <div class="mt-5 flex flex-col items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-5 py-4 text-center text-xs text-slate-500 shadow-card sm:flex-row sm:text-left">
            <p>
                Verifikasi sistem pada
                <span class="font-semibold text-slate-700">{{ now()->translatedFormat('d F Y - H:i') }}</span>
            </p>
            <a href="{{ route('asset.check') }}" class="inline-flex items-center gap-1.5 font-bold text-brand-700 transition hover:text-brand-900">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                Cek aset lain
            </a>
        </div>

        <div class="mt-5 flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-card">
            <span class="grid h-9 w-9 flex-none place-items-center rounded-xl bg-brand-50 text-brand-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <p class="text-[11px] leading-relaxed text-slate-500">
                Halaman ini dihasilkan dari tanda tangan digital unik aset. Jika data tidak sesuai dengan fisik aset, segera laporkan kepada Biro Sarpras melalui tombol <span class="font-semibold text-slate-700">Lapor Kerusakan</span>.
            </p>
        </div>
    </div>
@endsection
