@php
    use App\Models\User;

@endphp

<div>
    <div class="flex -space-x-4 rtl:space-x-reverse">
        @if ($getRecord()->details)
            @foreach ($getRecord()->details as $detail)
                @if (isset($detail->professionals))
                    @foreach ($detail->professionals as $professionalId)
                        @php
                            $user = \App\Models\User::find($professionalId);
                            if ($user) {
                                $avatar = $user->profile && $user->profile->avatar ? asset('storage/' . $user->profile->avatar) : 'https://ui-avatars.com/api/?name=' . str_replace(' ', '+', $user->name);
                            }
                        @endphp
                        @if ($user)
                            <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ $avatar }}" alt="{{ $user->name }}" title="{{ $user->name }}">
                        @endif
                    @endforeach
                @endif
            @endforeach
        @endif
    </div>
</div>
