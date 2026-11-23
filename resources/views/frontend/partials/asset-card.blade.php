@php
    $conditionValue = $asset->condition?->value;
    $conditionLabel = $asset->condition?->getLabel() ?? 'Tidak diketahui';
    $conditionClasses = match ($conditionValue) {
        'good' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'fair' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'damaged' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'bad' => 'bg-slate-100 text-slate-600 ring-slate-200',
        default => 'bg-slate-100 text-slate-600 ring-slate-200',
    };
    $conditionDot = match ($conditionValue) {
        'good' => 'bg-emerald-500',
        'fair' => 'bg-amber-500',
        'damaged' => 'bg-rose-500',
        default => 'bg-slate-400',
    };

    $statusValue = $asset->status?->value;
    $statusLabel = $asset->status?->getLabel() ?? '-';
    $statusClasses = match ($statusValue) {
        'available' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'in_use' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'maintenance' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'lost' => 'bg-rose-50 text-rose-700 ring-rose-200',
        default => 'bg-slate-100 text-slate-600 ring-slate-200',
    };
@endphp

<article class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-card">
    {{-- Header identitas --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-brand-900 via-brand-800 to-brand-950 px-5 py-6 text-white sm:px-7 sm:py-7">
        <div class="pointer-events-none absolute -right-12 -top-16 h-40 w-40 rounded-full bg-accent-400/10 blur-2xl" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-200">Kode Identitas Aset</p>
                <p id="asset-code" class="mt-1 break-all font-mono text-xl font-extrabold tracking-wide text-white sm:text-2xl">{{ $asset->asset_code }}</p>
            </div>
            <button
                type="button"
                class="inline-flex flex-none items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1.5 text-[11px] font-bold text-white backdrop-blur transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400 focus-visible:ring-offset-2 focus-visible:ring-offset-brand-900"
                data-copy="{{ $asset->asset_code }}"
                aria-label="Salin kode aset {{ $asset->asset_code }}"
            >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h7a2 2 0 012 2v7a2 2 0 01-2 2h-2M8 9H5a2 2 0 00-2 2v8a2 2 0 002 2h7a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg>
                <span data-copy-label>Salin</span>
            </button>
        </div>
        <h3 class="relative mt-4 text-lg font-bold leading-snug text-white sm:text-xl">{{ $asset->name }}</h3>
    </div>

    {{-- Ringkasan status --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-100 px-5 py-4 sm:px-7">
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $conditionClasses }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $conditionDot }}" aria-hidden="true"></span>
            Kondisi: {{ $conditionLabel }}
        </span>
        <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $statusClasses }}">
            Status: {{ $statusLabel }}
        </span>
    </div>

    {{-- Detail --}}
    <dl class="grid grid-cols-1 gap-px bg-slate-100 sm:grid-cols-2">
        <div class="bg-white px-5 py-4 sm:px-7">
            <dt class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Kategori</dt>
            <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $asset->category->name ?? '-' }}</dd>
        </div>
        <div class="bg-white px-5 py-4 sm:px-7">
            <dt class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Lokasi</dt>
            <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $asset->room->location->name ?? 'Belum ditentukan' }}</dd>
            <dd class="text-xs text-slate-500">{{ $asset->room->name ?? 'Ruang belum ditentukan' }}</dd>
        </div>
        <div class="bg-white px-5 py-4 sm:px-7">
            <dt class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Unit / Ruang</dt>
            <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $asset->room->name ?? '-' }}</dd>
        </div>
        <div class="bg-white px-5 py-4 sm:px-7">
            <dt class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Penanggung Jawab</dt>
            <dd class="mt-1.5 flex items-center gap-2.5">
                <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-brand-100 text-xs font-extrabold text-brand-800" aria-hidden="true">
                    {{ strtoupper(substr($asset->pic->name ?? 'U', 0, 1)) }}
                </span>
                <span class="text-sm font-semibold text-slate-800">{{ $asset->pic->name ?? 'Belum ada PIC' }}</span>
            </dd>
        </div>
    </dl>

    {{-- Aksi --}}
    <div class="flex flex-col gap-2.5 px-5 py-5 sm:flex-row sm:px-7">
        <button
            type="button"
            data-copy="{{ $asset->asset_code }}"
            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-brand-800 transition hover:border-brand-200 hover:bg-brand-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h7a2 2 0 012 2v7a2 2 0 01-2 2h-2M8 9H5a2 2 0 00-2 2v8a2 2 0 002 2h7a2 2 0 002-2V9a2 2 0 00-2-2z"/></svg>
            Salin Kode
        </button>
        @if($asset->qr_signature_hash)
            <a
                href="{{ route('asset.report', $asset->qr_signature_hash) }}"
                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700 transition hover:bg-rose-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 focus-visible:ring-offset-2"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Lapor Kerusakan
            </a>
        @endif
    </div>
</article>

@once
    @push('scripts')
        <script>
            (function () {
                if (window.__simaCopyBound) return;
                window.__simaCopyBound = true;

                document.addEventListener('click', function (event) {
                    const trigger = event.target.closest('[data-copy]');
                    if (!trigger) return;

                    const value = trigger.getAttribute('data-copy') || '';
                    const label = trigger.querySelector('[data-copy-label]');
                    const original = label ? label.textContent : null;

                    const done = function (ok) {
                        if (label) {
                            label.textContent = ok ? 'Tersalin' : 'Gagal';
                            window.setTimeout(function () {
                                label.textContent = original;
                            }, 1600);
                        }
                    };

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(value).then(function () { done(true); }).catch(function () { done(false); });
                        return;
                    }

                    const field = document.createElement('textarea');
                    field.value = value;
                    field.setAttribute('readonly', '');
                    field.style.position = 'absolute';
                    field.style.left = '-9999px';
                    document.body.appendChild(field);
                    field.select();

                    try {
                        done(document.execCommand('copy'));
                    } catch (error) {
                        done(false);
                    }

                    document.body.removeChild(field);
                });
            })();
        </script>
    @endpush
@endonce
