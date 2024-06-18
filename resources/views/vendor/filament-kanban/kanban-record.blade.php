@php
use App\Models\User;
use App\Enums\TaskPriorityEnum;
@endphp

<div id="{{ $record->getKey() }}" wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})" class="record border-2 border-gray-700 bg-gray-500 dark:border-gray-900 dark:bg-gray-700 rounded-lg px-6 py-5 cursor-grab font-medium text-gray-800 dark:text-gray-200" @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}) < 3) x-data x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 2000)
        " @endif>

        <div class="flex gap-4 justify-between items-center mb-4">

            <span class="text-sm font-semibold">
                {{ $record['title'] }}
            </span>

            @if ($record['priority'] === TaskPriorityEnum::HIGH)
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-600 inline-block" style="color: darkred" />
            @elseif ($record['priority'] === TaskPriorityEnum::MEDIUM)
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-warning-600 inline-block" style="color: darkgoldenrod" />
            @elseif ($record['priority'] === TaskPriorityEnum::LOW)
            <x-heroicon-o-exclamation-triangle class="h-5 w-5 text-success-600 inline-block" style="color: darkgreen" />
            @endif
        </div>

        <div class="mb-4 border-l-2" style="border-color: gray;">
            <p class=" text-xs font-light">{{ $record['description'] }} {{ $record['priority'] }}</p>
        </div>

        <div>
            <div class="flex hover:-space-x-1 -space-x-4">
                @php
                $professionals = User::whereIn('id', $record['professionals'])->get();
                @endphp
                @foreach ($professionals as $professional)
                <div class="transition-all">
                    <img class="h-10 w-10 rounded-full border border-white dark:border-white " src="{{ $professional->getFilamentAvatarUrl() }}" alt="{{ $professional->name }}">
                </div>
                @endforeach
            </div>
        </div>


</div>