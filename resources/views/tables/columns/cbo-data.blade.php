<div class="text-sm">
    @if ($getState())
        @foreach ($getState() as $value)
            @php
                $ocupation = Str::limit($value['ocupation'], 35, '...');
            @endphp
            {{ $value['code'] }} - {{ $ocupation }}
        @endforeach
    @endif
</div>
