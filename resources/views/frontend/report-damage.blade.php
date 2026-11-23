@extends('frontend.layouts.public')

@section('title', 'Lapor Kerusakan — '.$asset->asset_code)
@section('meta_description', 'Laporkan kerusakan aset '.$asset->asset_code.' kepada Biro Sarpras '.$appSettings->getInstitutionName().'.')

@section('content')
    <div class="relative overflow-hidden border-b border-rose-100 bg-gradient-to-b from-rose-50/80 to-white">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
            <a href="{{ route('asset.verify', $asset->qr_signature_hash) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 transition hover:text-rose-700">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke detail aset
            </a>

            <div class="mt-5 flex items-start gap-4">
                <span class="hidden h-14 w-14 flex-none place-items-center rounded-2xl bg-rose-600 text-white shadow-lg shadow-rose-600/20 sm:grid">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Lapor Kerusakan Aset</h1>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600">
                        Sampaikan kendala yang Anda temukan. Laporan akan diteruskan ke Biro Sarpras dan tercatat pada riwayat pemeliharaan aset.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        {{-- Ringkasan objek laporan --}}
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-card sm:p-5">
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Objek laporan</p>
                <p class="mt-1 truncate text-base font-extrabold text-slate-900">{{ $asset->name }}</p>
                <p class="mt-0.5 text-xs text-slate-500">
                    <span class="font-mono font-semibold text-slate-700">{{ $asset->asset_code }}</span>
                    &middot; {{ $asset->room->name ?? 'Lokasi tidak diketahui' }}
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-600 ring-1 ring-inset ring-slate-200">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Aset terverifikasi
            </span>
        </div>

        @if($errors->any())
            <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-800" role="alert">
                <svg class="mt-0.5 h-5 w-5 flex-none text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="text-sm font-extrabold">Laporan belum bisa dikirim</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5 text-xs text-rose-700">
                        @foreach($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('asset.report.submit', $asset->qr_signature_hash) }}" method="POST" class="space-y-5">
            @csrf

            {{-- Honeypot anti-bot --}}
            <div class="hidden" aria-hidden="true">
                <label for="website_url">Kosongkan kolom ini jika Anda manusia</label>
                <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-soft">
                <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4 sm:px-7">
                    <h2 class="text-sm font-extrabold uppercase tracking-[0.14em] text-slate-700">Detail Kendala</h2>
                </div>

                <div class="space-y-5 px-5 py-6 sm:px-7 sm:py-7">
                    <div>
                        <label for="problem_description" class="mb-1.5 block text-xs font-bold text-slate-500">
                            Jelaskan kendala / kerusakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            name="problem_description"
                            id="problem_description"
                            rows="5"
                            required
                            maxlength="255"
                            aria-required="true"
                            placeholder="Contoh: Layar bergaris dan sering berkedip, proyektor mati total, tinta printer habis…"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm leading-relaxed text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/25"
                        >{{ old('problem_description') }}</textarea>
                        <div class="mt-1.5 flex items-center justify-between gap-3">
                            <p class="text-[11px] text-slate-400">Sertakan gejala dan kondisi saat ini agar penanganan lebih cepat.</p>
                            <p class="flex-none text-[11px] font-semibold text-slate-400"><span data-char-count>0</span>/255</p>
                        </div>
                    </div>

                    <div>
                        <label for="reporter_name" class="mb-1.5 block text-xs font-bold text-slate-500">Nama Anda <span class="font-medium text-slate-400">(opsional)</span></label>
                        <input
                            type="text"
                            name="reporter_name"
                            id="reporter_name"
                            value="{{ old('reporter_name') }}"
                            maxlength="100"
                            autocomplete="name"
                            placeholder="Contoh: Budi — Mahasiswa Sistem Informasi"
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-rose-500 focus:bg-white focus:ring-2 focus:ring-rose-500/25"
                        >
                        <p class="mt-1.5 text-[11px] text-slate-400">Boleh dikosongkan jika ingin melapor secara anonim.</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-rose-600/20 transition hover:bg-rose-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Kirim Laporan Kerusakan
            </button>

            <p class="px-2 text-center text-[11px] leading-relaxed text-slate-400">
                Demi menjaga keamanan, sistem mencatat alamat IP perangkat Anda
                (<span class="font-mono text-slate-500">{{ request()->ip() }}</span>) saat mengirim laporan ini.
            </p>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const field = document.getElementById('problem_description');
            const counter = document.querySelector('[data-char-count]');
            if (!field || !counter) return;

            const update = function () { counter.textContent = String(field.value.length); };
            field.addEventListener('input', update);
            update();
        })();
    </script>
@endpush
