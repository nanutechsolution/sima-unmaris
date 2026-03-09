
<div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl mt-2 border border-gray-100 dark:border-gray-800 text-sm">
    <div class="flex items-start gap-3">
        <x-heroicon-o-information-circle class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" />
        
        <div class="space-y-1">
            <p class="text-gray-800 dark:text-gray-200">
                <span class="font-bold">Deskripsi Aksi:</span> 
                {{ $getRecord()->description }}
            </p>
            
            @if($getRecord()->subject_type)
                <p class="text-gray-500 dark:text-gray-400 text-xs font-mono bg-white dark:bg-gray-900 inline-block px-2 py-1 rounded shadow-sm border border-gray-200 dark:border-gray-700">
                    <span class="font-bold text-gray-700 dark:text-gray-300 font-sans">Target Objek:</span> 
                    {{ class_basename($getRecord()->subject_type) }} 
                    <span class="text-primary-500">(ID: {{ substr($getRecord()->subject_id, 0, 8) }}...)</span>
                </p>
            @endif
        </div>
    </div>
</div>