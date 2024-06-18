@props(['status'])

<div class="mb-5 flex flex-shrink-0 flex-col md:min-h-full md:w-[24rem]">
    @include(static::$headerView)

    <div class="flex flex-1 flex-col gap-2 rounded-xl bg-white p-3 dark:bg-gray-800" data-status-id="{{ $status['id'] }}">
        @foreach ($status['records'] as $record)
        @include(static::$recordView)
        @endforeach
    </div>
</div>