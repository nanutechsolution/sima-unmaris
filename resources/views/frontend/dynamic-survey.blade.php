<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey->title }} - Universitas Stella Maris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- logo icon -->
    <link rel="icon" href="{{ asset('images/logo-unmaris.png') }}" type="image/x-icon">
    <style>
        .rating-btn.active svg { color: #eab308; fill: #eab308; }
        .rating-btn:hover svg { color: #facc15; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">
    <div class="max-w-md mx-auto bg-white min-h-screen shadow-2xl flex flex-col relative">
        
        <!-- Header -->
        <div class="bg-[#1B2A66] text-white p-6 pb-12 text-center shadow-md relative z-10 rounded-b-3xl">
            <h1 class="text-xl font-extrabold tracking-wide uppercase leading-snug">{{ $survey->title }}</h1>
            @if($survey->description)
                <p class="text-[#FACC15] text-xs mt-2 font-medium opacity-90">{{ $survey->description }}</p>
            @endif
        </div>

        <div class="p-6 flex-1 -mt-8 relative z-20">
            
            {{-- LOGIKA 1: CEK IP (Sisi Server) --}}
            {{-- LOGIKA 2: CEK BROWSER STORAGE (Sisi Klien via JS di bawah) --}}
            
            <div id="already-submitted-view" class="{{ $alreadySubmitted ? '' : 'hidden' }}">
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 text-center space-y-4">
                    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Partisipasi Berhasil!</h2>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Terima kasih telah membantu meningkatkan kualitas layanan Universitas Stella Maris. Anda sudah memberikan kontribusi untuk survei ini.
                    </p>
                    <div class="pt-4 border-t border-gray-50 mt-4">
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Log Validasi Perangkat</p>
                        <p class="text-xs font-mono text-gray-500 mt-1">{{ request()->ip() }}</p>
                    </div>
                </div>
            </div>

            <div id="survey-form-view" class="{{ $alreadySubmitted ? 'hidden' : '' }}">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl flex items-start gap-3 shadow-sm mb-6">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-medium leading-relaxed">{{ session('success') }}</p>
                    </div>
                    <script>
                        // Jika sukses submit, tandai browser ini agar tidak bisa isi lagi
                        localStorage.setItem('survey_submitted_{{ $survey->id }}', 'true');
                    </script>
                @endif

                <form id="surveyForm" action="{{ route('survey.submit', $survey->id) }}" method="POST" class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 space-y-6">
                    @csrf
                    <div class="hidden" aria-hidden="true">
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="pb-6 mb-2 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-widest">Identitas Diri</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Nama (Opsional)</label>
                                <input type="text" name="responder_name" class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#1B2A66] bg-gray-50 outline-none transition" placeholder="Boleh Anonim">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Status Peran <span class="text-red-500">*</span></label>
                                <select name="responder_type" required class="w-full px-4 py-2.5 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#1B2A66] bg-gray-50 outline-none transition">
                                    <option value="Mahasiswa">Mahasiswa</option>
                                    <option value="Dosen">Dosen</option>
                                    <option value="Staf/Tendik">Staf/Tendik</option>
                                    <option value="Tamu">Tamu</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-800 mb-2 text-sm uppercase tracking-widest">Pertanyaan</h3>
                    
                    @foreach($survey->form_schema as $index => $field)
                        @php
                            $type = $field['type'];
                            $data = $field['data'];
                            $question = $data['question'] ?? 'Pertanyaan ' . ($index + 1);
                            $isRequired = $data['is_required'] ?? false;
                            $fieldName = 'answer_' . $index;
                        @endphp

                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-3">
                                {{ $loop->iteration }}. {{ $question }} 
                                @if($isRequired) <span class="text-red-500">*</span> @endif
                            </label>

                            @if($type === 'text')
                                <input type="text" name="answers[{{ $fieldName }}]" {{ $isRequired ? 'required' : '' }} class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#1B2A66] bg-white outline-none transition">
                            @elseif($type === 'textarea')
                                <textarea name="answers[{{ $fieldName }}]" rows="3" {{ $isRequired ? 'required' : '' }} class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#1B2A66] bg-white outline-none transition"></textarea>
                            @elseif($type === 'select')
                                <select name="answers[{{ $fieldName }}]" {{ $isRequired ? 'required' : '' }} class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#1B2A66] bg-white outline-none transition">
                                    <option value="" disabled selected>Pilih...</option>
                                    @foreach($data['options'] ?? [] as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            @elseif($type === 'rating')
                                <div class="flex justify-between items-center bg-white p-2 rounded-xl border border-gray-200 rating-container">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" data-value="{{ $i }}" class="rating-btn p-1 transition transform hover:scale-110 focus:outline-none">
                                            <svg class="w-8 h-8 text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="answers[{{ $fieldName }}]" class="rating-input" {{ $isRequired ? 'required' : '' }}>
                            @endif
                        </div>
                    @endforeach

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#1B2A66] hover:bg-blue-900 text-white font-bold py-4 px-4 rounded-xl shadow-lg transition-all active:scale-95">
                            Kirim Jawaban
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="text-center pb-6">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} UNMARIS - Biro Sarpras</p>
        </div>
    </div>

    <script>
        // --- PROTEKSI LAPIS KEDUA: LOCAL STORAGE (CLIENT SIDE) ---
        // Cek apakah di browser ini sudah ada tanda pernah mengisi survei ini
        if (localStorage.getItem('survey_submitted_{{ $survey->id }}')) {
            document.getElementById('survey-form-view').classList.add('hidden');
            document.getElementById('already-submitted-view').classList.remove('hidden');
        }

        // --- RATING STARS LOGIC ---
        document.querySelectorAll('.rating-container').forEach(container => {
            const buttons = container.querySelectorAll('.rating-btn');
            const input = container.nextElementSibling;
            buttons.forEach((btn, index) => {
                btn.addEventListener('click', () => {
                    input.value = btn.getAttribute('data-value');
                    buttons.forEach(b => b.classList.remove('active'));
                    for(let i = 0; i <= index; i++) {
                        buttons[i].classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html>