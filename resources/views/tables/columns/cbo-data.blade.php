<div class="text-sm">
    @if ($getState())
        @foreach ($getState() as $value)
            @php
                $occupation = Str::limit($value['occupation'], 35, '...');
            @endphp
            {{ $occupation }}
        @endforeach
    @endif
</div>
