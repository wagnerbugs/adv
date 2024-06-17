@php
    use Illuminate\Support\Facades\Bus;
    use Illuminate\Support\Facades\DB;

    $batch_db = DB::table('job_batches')
        ->where('name', $getRecord()->details->process_api_id)
        ->first();

    $batch = null;
    if (!is_null($batch_db)) {
        $batch = Bus::findBatch($batch_db->id);
    }

@endphp

<div class="w-full px-4">
    <div class="">
        <div class="bg-secondary-200 h-1.5 rounded-full dark:bg-gray-700">
            @if (!is_null($batch))
                <div class="bg-primary-600 h-1.5 rounded-full text-sm" style="width: {{ $batch->progress() }}%">
                    <small>{{ $batch->progress() }}%</small>
                </div>
            @endif
        </div>
    </div>
</div>
