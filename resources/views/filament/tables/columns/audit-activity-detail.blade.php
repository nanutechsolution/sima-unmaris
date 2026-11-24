
<div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm dark:border-gray-800 dark:bg-gray-800/40">
    <div class="flex items-start gap-3">
        <x-filament::icon icon="heroicon-o-information-circle" class="mt-0.5 h-5 w-5 flex-shrink-0 text-gray-400" />

        <div class="min-w-0 space-y-1">
            <p class="break-words text-gray-800 dark:text-gray-200">
                <span class="font-bold">Deskripsi Aksi:</span>
                {{ $getRecord()->description }}
            </p>

            @if($getRecord()->subject_type)
                <p class="inline-block max-w-full break-words rounded border border-gray-200 bg-white px-2 py-1 font-mono text-xs text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                    <span class="font-sans font-bold text-gray-700 dark:text-gray-300">Target Objek:</span>
                    {{ class_basename($getRecord()->subject_type) }}
                    <span class="text-primary-500">(ID: {{ substr($getRecord()->subject_id, 0, 8) }}...)</span>
                </p>
            @endif
        </div>
    </div>
</div>