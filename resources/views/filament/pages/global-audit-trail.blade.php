<x-filament-panels::page>
    {{-- Menggunakan komponen native Filament agar terintegrasi sempurna dengan tema dan Dark Mode --}}
    <x-filament::section>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            {{-- Ikon Filament (bukan blade-icon) karena panel menonaktifkan komponen heroicon bawaan --}}
            <div class="grid h-12 w-12 flex-none place-items-center rounded-xl bg-primary-50 text-primary-600 ring-1 ring-primary-100 dark:bg-primary-900/30 dark:text-primary-400 dark:ring-primary-900/50">
                <x-filament::icon icon="heroicon-o-shield-check" class="h-6 w-6" />
            </div>
            <div class="min-w-0">
                <h2 class="text-lg font-bold tracking-tight text-gray-800 dark:text-white sm:text-xl">
                    Pusat Pantauan Aktivitas (Audit Trail)
                </h2>
                <p class="mt-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                    Seluruh rekam jejak pembuatan, perubahan, dan penghapusan data di dalam
                    {{ $appSettings->getAppName() }} dicatat secara permanen di halaman ini demi
                    transparansi dan keamanan.
                </p>
            </div>
        </div>
    </x-filament::section>
    {{ $this->table }}
</x-filament-panels::page>
