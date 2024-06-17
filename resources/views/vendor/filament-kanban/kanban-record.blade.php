@php
use App\Models\User;
use App\Enums\TaskPriorityEnum;
@endphp
<div id="{{ $record->getKey() }}" wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})" class="record border-l-2 {{ $record['priority'] === TaskPriorityEnum::HIGH->value ? 'border-danger-500' : ($record['priority'] === TaskPriorityEnum::MEDIUM->value ? 'border-warning-500' : 'border-success-500') }} bg-white dark:bg-gray-700 rounded-lg px-4 py-2 cursor-grab font-medium text-gray-600 dark:text-gray-200" @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}) < 3) x-data x-init="$el.classList.add('animate-pulse-twice', 'bg-primary-400', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')" @endif>

        <div class="flex gap-4 justify-between items-center">

            {{ $record['title'] }}

            @if($record['is_urgent'] === 1)
            <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-danger-600 inline-block" />
            @endif
        </div>

        <div>
            <p class="text-sm">{{ $record['description'] }}</p>
        </div>

        <div>
            <div class="flex -space-x-4 rtl:space-x-reverse mt-3">
                @php
                $professionals = User::whereIn('id', $record['professionals'])->get();
                @endphp
                @foreach ($professionals as $professional)
                <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ $professional->getFilamentAvatarUrl() }}" alt="{{ $professional->name }}">
                @endforeach
            </div>
        </div>


</div>