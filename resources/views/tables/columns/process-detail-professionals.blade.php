@php
    // Coletar todos os IDs de profissionais e remover duplicatas
    $professionalIds = collect($getRecord()->details)
        ->pluck('professionals')
        ->flatten()
        ->unique()
        ->toArray();

    // Buscar todos os usuários com esses IDs
    $users = \App\Models\User::with('profile')->whereIn('id', $professionalIds)->get()->keyBy('id');
@endphp

<div>
    <div class="flex -space-x-4 rtl:space-x-reverse">
        @foreach ($users as $user)
            @php
                $avatar = $user->profile && $user->profile->avatar ? asset('storage/' . $user->profile->avatar) : 'https://ui-avatars.com/api/?name=' . str_replace(' ', '+', $user->name);
            @endphp
            <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ $avatar }}" alt="{{ $user->name }}" title="{{ $user->name }}">
        @endforeach
    </div>
</div>
