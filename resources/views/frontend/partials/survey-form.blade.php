@php
    $alreadySubmitted = $alreadySubmitted ?? false;
    $requiredCount = collect($survey->form_schema)
        ->filter(fn ($field) => (bool) ($field['data']['is_required'] ?? false))
        ->count();
    $totalRequired = $requiredCount + 1; // +1 untuk status peran (wajib)
@endphp

@if(session('success'))
    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800" role="status">
        <svg class="mt-0.5 h-5 w-5 flex-none text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-semibold leading-relaxed">{{ session('success') }}</p>
    </div>
    <script>
        try { localStorage.setItem('survey_submitted_{{ $survey->id }}', 'true'); } catch (e) {}
    </script>
@endif

{{-- ==================== SUDAH MENGISI ==================== --}}
<div id="already-submitted-view" class="{{ $alreadySubmitted ? '' : 'hidden' }}">
    <div class="rounded-3xl border border-slate-200/80 bg-white p-8 text-center shadow-card sm:p-10">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 ring-8 ring-emerald-50/60">
            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="mt-5 text-xl font-extrabold text-slate-900">Partisipasi Berhasil!</h2>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-slate-600">
            Terima kasih telah membantu meningkatkan kualitas layanan {{ $appSettings->getInstitutionName() }}. Anda sudah memberikan kontribusi untuk survei ini.
        </p>
        <div class="mt-6 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Log Validasi Perangkat</p>
            <p class="mt-1 font-mono text-xs text-slate-500">{{ request()->ip() }}</p>
        </div>
        <a href="{{ route('home') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-800 px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"/></svg>
            Kembali ke Beranda
        </a>
    </div>
</div>

{{-- ==================== FORM SURVEI ==================== --}}
<div id="survey-form-view" class="{{ $alreadySubmitted ? 'hidden' : '' }}">
    <form id="surveyForm" action="{{ route('survey.submit', $survey->id) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Honeypot anti-bot --}}
        <div class="hidden" aria-hidden="true">
            <label for="website_url">Kosongkan kolom ini jika Anda manusia</label>
            <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
        </div>

        {{-- Progress --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-card sm:p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Kemajuan Pengisian</p>
                <p class="text-xs font-bold text-brand-800" data-progress-label>{{ $totalRequired }} pertanyaan wajib</p>
            </div>
            <div class="mt-2.5 h-2 overflow-hidden rounded-full bg-slate-100"
                 role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="Kemajuan pengisian survei">
                <div data-progress-bar class="h-full w-0 rounded-full bg-gradient-to-r from-brand-700 to-brand-500 transition-[width] duration-300"></div>
            </div>
        </div>

        {{-- Identitas --}}
        <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-card sm:p-6">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <h2 class="text-sm font-extrabold uppercase tracking-[0.14em] text-slate-700">Identitas Diri</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="responder_name" class="mb-1.5 block text-xs font-bold text-slate-500">Nama <span class="font-medium text-slate-400">(opsional)</span></label>
                    <input type="text" name="responder_name" id="responder_name" maxlength="100" autocomplete="name" placeholder="Boleh anonim"
                           class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-600/30">
                </div>
                <div data-progress-item>
                    <label for="responder_type" class="mb-1.5 block text-xs font-bold text-slate-500">Status Peran <span class="text-rose-500">*</span></label>
                    <select name="responder_type" id="responder_type" required
                            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-600/30">
                        <option value="Mahasiswa">Mahasiswa</option>
                        <option value="Dosen">Dosen</option>
                        <option value="Staf/Tendik">Staf/Tendik</option>
                        <option value="Tamu">Tamu</option>
                    </select>
                </div>
            </div>
        </section>

        {{-- Pertanyaan --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3 px-1">
                <h2 class="text-sm font-extrabold uppercase tracking-[0.14em] text-slate-700">Pertanyaan Survei</h2>
                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-700 ring-1 ring-inset ring-amber-200">{{ $requiredCount }} wajib</span>
            </div>

            @foreach($survey->form_schema as $index => $field)
                @php
                    $type = $field['type'];
                    $data = $field['data'];
                    $question = $data['question'] ?? 'Pertanyaan '.($index + 1);
                    $isRequired = $data['is_required'] ?? false;
                    $fieldName = 'answer_'.$index;
                    $inputId = 'answer-'.$index;
                @endphp

                <fieldset class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-card transition focus-within:border-brand-300 sm:p-6"
                          @if($isRequired) data-progress-item @endif>
                    <legend class="mb-3 text-sm font-bold leading-relaxed text-slate-800">
                        <span class="mr-1.5 inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-50 align-middle text-xs font-extrabold text-brand-700">{{ $loop->iteration }}</span>
                        <span class="align-middle">{{ $question }}</span>
                        @if($isRequired)<span class="ml-1 align-middle text-rose-500" aria-hidden="true">*</span><span class="sr-only">(wajib diisi)</span>@endif
                    </legend>

                    @if($type === 'text')
                        <input type="text" id="{{ $inputId }}" name="answers[{{ $fieldName }}]" placeholder="Tulis jawaban Anda…"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-600/30"
                               @if($isRequired) required aria-required="true" @endif>

                    @elseif($type === 'textarea')
                        <textarea id="{{ $inputId }}" name="answers[{{ $fieldName }}]" rows="4" placeholder="Tuliskan secara detail…"
                                  class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-600/30"
                                  @if($isRequired) required aria-required="true" @endif></textarea>

                    @elseif($type === 'select')
                        <select id="{{ $inputId }}" name="answers[{{ $fieldName }}]"
                                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-brand-600 focus:bg-white focus:ring-2 focus:ring-brand-600/30"
                                @if($isRequired) required aria-required="true" @endif>
                            <option value="" disabled selected>Pilih salah satu…</option>
                            @foreach($data['options'] ?? [] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>

                    @elseif($type === 'rating')
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3">
                            <div class="rating-row" role="radiogroup" aria-label="{{ $question }}">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="{{ $inputId }}-{{ $i }}" name="answers[{{ $fieldName }}]" value="{{ $i }}"
                                           @if($isRequired) required @endif>
                                    <label for="{{ $inputId }}-{{ $i }}" title="{{ $i }} dari 5">
                                        <svg class="h-8 w-8 sm:h-9 sm:w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        <span class="sr-only">{{ $i }} dari 5</span>
                                    </label>
                                @endfor
                            </div>
                            <div class="mt-2 flex justify-between text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                <span>Sangat buruk</span>
                                <span>Sangat baik</span>
                            </div>
                        </div>
                    @endif
                </fieldset>
            @endforeach
        </section>

        <div class="sticky bottom-4 z-10 rounded-2xl border border-slate-200/80 bg-white/90 p-3 shadow-soft backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
            <button type="submit" id="survey-submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-800 px-5 py-4 text-sm font-extrabold text-white shadow-lg shadow-brand-800/20 transition hover:bg-brand-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                <span data-submit-label>Kirim Jawaban</span>
            </button>
            <p class="mt-3 text-center text-[11px] leading-relaxed text-slate-400">
                Dengan mengirim, Anda menyetujui jawaban dicatat beserta alamat IP (<span class="font-mono">{{ request()->ip() }}</span>) untuk mencegah pengiriman ganda.
            </p>
        </div>
    </form>
</div>

@once
    @push('scripts')
        <script>
            (function () {
                const surveyId = '{{ $survey->id }}';
                const formView = document.getElementById('survey-form-view');
                const alreadyView = document.getElementById('already-submitted-view');

                // Lapisan klien: sembunyikan form bila browser ini sudah pernah mengirim.
                try {
                    if (localStorage.getItem('survey_submitted_' + surveyId)) {
                        if (formView) formView.classList.add('hidden');
                        if (alreadyView) alreadyView.classList.remove('hidden');
                    }
                } catch (e) {}

                const form = document.getElementById('surveyForm');
                if (!form) return;

                const items = form.querySelectorAll('[data-progress-item]');
                const bar = form.querySelector('[data-progress-bar]');
                const label = form.querySelector('[data-progress-label]');
                const track = bar ? bar.parentElement : null;

                const isAnswered = function (item) {
                    const radios = item.querySelectorAll('input[type="radio"]');
                    if (radios.length) {
                        return Array.prototype.some.call(radios, function (radio) { return radio.checked; });
                    }
                    const control = item.querySelector('input:not([type="hidden"]):not([type="radio"]), textarea, select');
                    return !!control && control.value.trim() !== '';
                };

                const updateProgress = function () {
                    if (!bar || !track) return;
                    const total = items.length;
                    let done = 0;
                    items.forEach(function (item) {
                        if (isAnswered(item)) done += 1;
                    });
                    const pct = total > 0 ? Math.round((done / total) * 100) : 100;
                    bar.style.width = pct + '%';
                    track.setAttribute('aria-valuenow', String(pct));
                    if (label) {
                        label.textContent = total > 0 ? done + ' dari ' + total + ' wajib terisi' : 'Semua pertanyaan opsional';
                    }
                };

                form.addEventListener('input', updateProgress);
                form.addEventListener('change', updateProgress);
                updateProgress();

                // Cegah pengiriman ganda.
                let submitting = false;
                form.addEventListener('submit', function (event) {
                    if (submitting) {
                        event.preventDefault();
                        return;
                    }
                    submitting = true;

                    const button = document.getElementById('survey-submit');
                    const submitLabel = button ? button.querySelector('[data-submit-label]') : null;
                    if (button) {
                        button.disabled = true;
                        button.setAttribute('aria-busy', 'true');
                    }
                    if (submitLabel) submitLabel.textContent = 'Mengirim…';
                });
            })();
        </script>
    @endpush
@endonce
