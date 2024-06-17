<div class="flex justify-between items-center mb-2 px-4 py-3 text-lg font-bold bg-gray-800 text-violet-500 uppercase text-center rounded-lg">
    {{ $status['title'] }}
    <div class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-gray-300  border-2 rounded-full dark:border-gray-700">{{ count($status['records']) }}</div>
</div>