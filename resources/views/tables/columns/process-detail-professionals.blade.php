@php
    use App\Models\User;
@endphp

<div>
    <div class="flex -space-x-4 rtl:space-x-reverse">
        @if ($getRecord()->professionals)
            @foreach ($getRecord()->professionals as $professional)
                @php
                    $user = User::with('profile')->find($professional);
                    if ($user->profile->avatar === null) {
                        $avatar = 'https://ui-avatars.com/api/?name=' . str_replace(' ', '+', $user->name);
                    } else {
                        $avatar = '/storage/' . $user->profile->avatar;
                    }

                @endphp
                <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ $avatar }}" alt="{{ $user->name }}" title="{{ $user->name }}">
            @endforeach
        @endif
    </div>
</div>
