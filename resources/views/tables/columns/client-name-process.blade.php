@php
    use App\Enums\ClientTypeEnum;
@endphp

<div>
    @if ($getRecord()->client->type === ClientTypeEnum::INDIVIDUAL)
        <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
            <div class="flex">
                <div class="flex max-w-max">
                    <div class="fi-ta-text-item fi-color-custom fi-color-primary inline-flex items-center gap-1.5">
                        <span
                            class="fi-ta-text-item-label text-custom-600 dark:text-custom-400 text-sm font-semibold leading-6"
                            style="--c-400:var(--primary-400);--c-600:var(--primary-600);">{{ $getRecord()->client->individual->name }}</span>
                    </div>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $getRecord()->client->document }}</p>
        </div>
    @elseif ($getRecord()->client->type === ClientTypeEnum::COMPANY)
        <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
            <div class="flex">
                <div class="flex max-w-max">
                    <div class="fi-ta-text-item fi-color-custom fi-color-primary inline-flex items-center gap-1.5">
                        <span
                            class="fi-ta-text-item-label text-custom-600 dark:text-custom-400 text-sm font-semibold leading-6"
                            style="--c-400:var(--primary-400);--c-600:var(--primary-600);">{{ $getRecord()->client->company->company }}</span>
                    </div>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $getRecord()->client->document }}</p>
        </div>
    @else
        ---
    @endif
</div>
