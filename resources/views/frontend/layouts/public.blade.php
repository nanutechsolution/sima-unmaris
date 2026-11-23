<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0C1330">
    <title>@yield('title', $appSettings->getAppName().' — '.$appSettings->getInstitutionName())</title>
    <meta name="description" content="@yield('meta_description', $appSettings->getAppName().': '.$appSettings->getAppDescription())">
    <link rel="icon" href="{{ $appSettings->getFaviconUrl() }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f2f6fc',
                            100: '#e3ebf7',
                            200: '#c6d7ee',
                            300: '#9cb9e0',
                            400: '#6c94cf',
                            500: '#4a75bb',
                            600: '#385ca0',
                            700: '#2e4a82',
                            800: '#1B2A66',
                            900: '#131f4d',
                            950: '#0C1330',
                        },
                        accent: {
                            300: '#fde68a',
                            400: '#FACC15',
                            500: '#F59E0B',
                            600: '#d97706',
                        },
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        soft: '0 24px 60px -24px rgba(12, 19, 48, 0.28)',
                        card: '0 2px 12px -4px rgba(12, 19, 48, 0.10)',
                    },
                },
            },
        };
    </script>

    <style>
        /* Rating bintang berbasis radio asli (tanpa JS, aksesibel keyboard) */
        .rating-row { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: .3rem; }
        .rating-row input { position: absolute; width: 1px; height: 1px; opacity: 0; }
        .rating-row label { color: #cbd5e1; cursor: pointer; transition: color .15s ease, transform .15s ease; }
        .rating-row label:hover,
        .rating-row label:hover ~ label,
        .rating-row input:checked ~ label { color: #F59E0B; }
        .rating-row label:hover { transform: scale(1.08); }
        .rating-row input:focus-visible + label { outline: 2px solid #1B2A66; outline-offset: 3px; border-radius: .6rem; }

        ::selection { background: #FACC15; color: #0C1330; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>

    @stack('head')
</head>
<body class="flex min-h-full flex-col bg-slate-50 text-slate-800 antialiased">

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-xl focus:bg-brand-800 focus:px-4 focus:py-2.5 focus:text-sm focus:font-semibold focus:text-white">
        Lewati ke konten utama
    </a>

    {{-- ======================= HEADER ======================= --}}
    <header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/85 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:h-20 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2">
                <img src="{{ $appSettings->getLogoUrl() }}" alt="Logo {{ $appSettings->getAppName() }}" class="h-10 w-10 flex-none object-contain sm:h-11 sm:w-11">
                <span class="min-w-0">
                    <span class="block truncate text-sm font-extrabold leading-tight tracking-wide text-brand-800 sm:text-base">{{ $appSettings->getAppName() }}</span>
                    <span class="block truncate text-[10px] font-semibold uppercase leading-tight tracking-[0.14em] text-slate-500 sm:text-[11px]">{{ $appSettings->getInstitutionName() }}</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-50 hover:text-brand-800">Beranda</a>
                <a href="{{ route('home') }}#alur-verifikasi" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-50 hover:text-brand-800">Alur Verifikasi</a>
                <a href="{{ route('home') }}#fitur" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-50 hover:text-brand-800">Fitur</a>
                <a href="{{ route('asset.check') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-brand-50 hover:text-brand-800">Cek Aset</a>
            </nav>

            <div class="flex flex-none items-center gap-2">
                <a href="{{ route('asset.check') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-brand-800 shadow-sm transition hover:border-brand-200 hover:bg-brand-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 sm:hidden">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                    Cek Aset
                </a>
                <a href="{{ url('/admin/login') }}" class="hidden items-center gap-2 rounded-xl bg-brand-800 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-800/20 transition hover:bg-brand-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 sm:inline-flex">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Portal Staf
                </a>
                <button type="button" id="nav-toggle" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 lg:hidden" aria-controls="mobile-nav" aria-expanded="false" aria-label="Buka menu navigasi">
                    <svg id="nav-icon-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
                    <svg id="nav-icon-close" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div id="mobile-nav" class="hidden border-t border-slate-200 bg-white lg:hidden">
            <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4 sm:px-6" aria-label="Navigasi seluler">
                <a href="{{ route('home') }}" class="block rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-800">Beranda</a>
                <a href="{{ route('home') }}#alur-verifikasi" class="block rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-800">Alur Verifikasi</a>
                <a href="{{ route('home') }}#fitur" class="block rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-800">Fitur</a>
                <a href="{{ route('asset.check') }}" class="block rounded-xl px-3 py-3 text-sm font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-800">Cek Status Aset</a>
                <a href="{{ url('/admin/login') }}" class="mt-2 flex items-center justify-center gap-2 rounded-xl bg-brand-800 px-4 py-3 text-sm font-bold text-white transition hover:bg-brand-900">Portal Staf</a>
            </nav>
        </div>
    </header>

    {{-- ======================= KONTEN ======================= --}}
    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    {{-- ======================= FOOTER ======================= --}}
    <footer class="mt-auto bg-brand-950 text-slate-400">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3">
                        <img src="{{ $appSettings->getLogoUrl() }}" alt="Logo {{ $appSettings->getAppName() }}" class="h-11 w-11 object-contain">
                        <div>
                            <p class="text-base font-extrabold leading-tight tracking-wide text-white">{{ $appSettings->getAppName() }}</p>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">{{ $appSettings->getInstitutionName() }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed">{{ $appSettings->getAppDescription() }}.</p>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-white">Tautan Cepat</h2>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a></li>
                        <li><a href="{{ route('home') }}#alur-verifikasi" class="transition hover:text-white">Alur Verifikasi Aset</a></li>
                        <li><a href="{{ route('home') }}#fitur" class="transition hover:text-white">Fitur Aplikasi</a></li>
                        <li><a href="{{ route('home') }}#faq" class="transition hover:text-white">Pertanyaan Umum</a></li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-white">Layanan Publik</h2>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><a href="{{ route('asset.check') }}" class="transition hover:text-white">Cek Status Aset</a></li>
                        <li><a href="{{ route('asset.check') }}" class="transition hover:text-white">Verifikasi QR Code</a></li>
                        <li><a href="{{ url('/admin/login') }}" class="transition hover:text-white">Portal Staf &amp; Administrator</a></li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-white">Kontak</h2>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li class="flex gap-2">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Jl. Karya Kasih No. 5, Tambolaka, Kab. Sumba Barat Daya</span>
                        </li>
                        <li class="flex gap-2">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:rektorat@unmaris.ac.id" class="transition hover:text-white">rektorat@unmaris.ac.id</a>
                        </li>
                        <li class="flex gap-2">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h2.28a1 1 0 01.95.68l1.2 3.6a1 1 0 01-.27 1.06L7.9 9.55a14 14 0 006.55 6.55l1.21-1.26a1 1 0 011.06-.27l3.6 1.2a1 1 0 01.68.95V19a2 2 0 01-2 2h-1C9.72 21 3 14.28 3 6V5z"/></svg>
                            <span>(021) 1234567</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-center text-xs text-slate-500 sm:flex-row sm:px-6 sm:text-left lg:px-8">
                <p>&copy; {{ date('Y') }} {{ $appSettings->getAppName() }} &middot; Biro Sarpras {{ $appSettings->getInstitutionName() }}</p>
                <p>Dikembangkan untuk pengelolaan aset kampus yang transparan.</p>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const button = document.getElementById('nav-toggle');
            const nav = document.getElementById('mobile-nav');
            const iconOpen = document.getElementById('nav-icon-open');
            const iconClose = document.getElementById('nav-icon-close');

            if (!button || !nav) return;

            const setOpen = function (open) {
                nav.classList.toggle('hidden', !open);
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (iconOpen) iconOpen.classList.toggle('hidden', open);
                if (iconClose) iconClose.classList.toggle('hidden', !open);
            };

            button.addEventListener('click', function () {
                setOpen(nav.classList.contains('hidden'));
            });

            nav.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () { setOpen(false); });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') setOpen(false);
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
