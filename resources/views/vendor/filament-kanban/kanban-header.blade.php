<div class="flex justify-between items-center mb-2 px-4 py-3 text-lg font-bold bg-white dark:bg-gray-800 text-violet-500 uppercase text-center rounded-lg">
    {{ $status['title'] }}
    <div class="inline-flex items-center justify-center w-6 h-6 text-xs">
        {{ count($status['records']) }}
    </div>
</div>