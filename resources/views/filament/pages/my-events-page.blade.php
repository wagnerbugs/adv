@php
use Carbon\Carbon;
use App\Enums\ClientTypeEnum;
use App\Models\User;
@endphp
<x-filament-panels::page>
    <div class="flex gap-6">
        <div class="w-2/3">
            @livewire(\App\Filament\Widgets\MyEventsWidget::class)
        </div>
        <div class="w-1/3">
            <div class="fi-section rounded-xl bg-white py-4 pl-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="col-span-1 max-h-[calc(100vh-15rem)] overflow-y-scroll p-4">
                    <ol class="relative border-s border-gray-200 dark:border-gray-700">
                        @foreach ($events as $event)
                        <li class="mb-10 ms-6">
                            <span class="ring-3 bg-primary-100 dark:bg-primary-900 absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full ring-white dark:ring-gray-900">
                                <svg class="text-primary-800 dark:text-primary-300 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </span>
                            <h3 class="mb-1 flex items-center text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $event->title }}
                                @foreach ($event->tags as $tag)
                                <span class="flex items-center text-[9px] uppercase bg-primary-100 text-primary-800 me-2 px-2.5 py-0.3 rounded dark:bg-primary-900 dark:text-primary-300 ms-3">
                                    <span class="w-2 h-2 me-2 rounded-full" style="background-color: {{ $tag->color }};"></span>
                                    {{ $tag->title }}
                                </span>
                                @endforeach
                            </h3>
                            <time class="mb-2 block text-xs font-normal leading-none text-gray-400 dark:text-gray-500">
                                Agendado para {{ Carbon::parse($event->starts_at)->format('d/m/Y H:i') }} às {{ Carbon::parse($event->ends_at)->format('d/m/Y H:i') }}
                            </time>
                            <p class="mb-4 text-sm font-normal text-gray-500 dark:text-gray-400">{{ $event->description }}</p>
                            @if($event->process_id)
                            <a href="{{ route('filament.admin.resources.processes.edit', $event->process_id) }}" class="inline-flex items-center px-4 py-2 text-xs font-thin text-gray-900 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                {{ $event->process->process }}
                            </a>
                            @endif
                            @if($event->client_id)
                            @if ($event->client->type === ClientTypeEnum::INDIVIDUAL)
                            <a href="{{ route('filament.admin.resources.client-individuals.edit', $event->client_id) }}" class="inline-flex items-center px-4 py-2 text-xs font-thin text-gray-900 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                {{ $event->client->name }}
                            </a>
                            @elseif ($event->client->type === ClientTypeEnum::COMPANY)
                            <a href="{{ route('filament.admin.resources.client-companies.edit', $event->client_id) }}" class="inline-flex items-center px-4 py-2 text-xs font-thin text-gray-900 bg-gray-100 border border-gray-200 rounded-lg hover:bg-gray-200 hover:text-blue-700 focus:z-10 focus:ring-4 focus:outline-none focus:ring-gray-100 focus:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                {{ $event->client->name }}
                            </a>
                            @endif
                            @endif
                            <div class="flex -space-x-4 rtl:space-x-reverse mt-3">
                                @php
                                $professionals = User::whereIn('id', $event->professionals)->get();
                                @endphp
                                @foreach ($professionals as $professional)
                                <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ $professional->getFilamentAvatarUrl() }}" alt="{{ $professional->name }}">
                                @endforeach
                            </div>
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>