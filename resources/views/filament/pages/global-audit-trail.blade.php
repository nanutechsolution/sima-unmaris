<x-filament-panels::page>
    {{-- Menggunakan komponen native Filament agar terintegrasi sempurna dengan tema dan Dark Mode --}}
    <x-filament::section>
        <div class="flex items-center gap-4">
            {{-- Menggunakan warna 'primary' bawaan Filament alih-alih warna statis --}}
            <div class="p-3 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl">
                <!-- <x-heroicon-o-shield-exclamation class="w-8 h-8" /> -->
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">
                    Pusat Pantauan Aktivitas (Audit Trail)
                </h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                    Seluruh rekam jejak pembuatan, perubahan, dan penghapusan data di dalam SIMA UNMARIS dicatat secara permanen di halaman ini demi transparansi dan keamanan.
                </p>
            </div>
        </div>
    </x-filament::section>
    {{ $this->table }}
</x-filament-panels::page>