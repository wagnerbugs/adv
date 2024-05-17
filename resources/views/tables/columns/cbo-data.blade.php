<div class="text-sm">
    @foreach ($getState() as $value)
        @php
            $ocupation = Str::limit($value['ocupation'], 35, '...');
        @endphp
        {{ $value['code'] }} - {{ $ocupation }}
    @endforeach
</div>
