@php
use App\Models\User;
use App\Enums\TaskPriorityEnum;
@endphp

<div class="p-4 border-l-2 border-gray-950 cursor-grab" id="{{ $record->getKey() }}" wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})">

    <div class=" flex gap-4 justify-between items-center mb-4">

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