<x-filament-panels::page>
    <div class="flex gap-6">
        <div class="w-2/3">
            @livewire(\App\Filament\Widgets\MyEventsWidget::class)
        </div>
        <div class="w-1/3">

            <div class="fi-section rounded-xl bg-white py-4 pl-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="col-span-1 max-h-[calc(100vh-15rem)] overflow-y-scroll p-4">

                    <ol class="relative border-s border-gray-200 dark:border-gray-700">
                        <li class="mb-10 ms-6">
                            <span class="ring-3 bg-primary-100 dark:bg-primary-900 absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full ring-white dark:ring-gray-900">
                                <svg class="text-primary-800 dark:text-primary-300 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </span>
                            <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-900 dark:text-white">Evento 01 <span
                                    class="bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-300 me-2 ms-3 rounded px-2.5 py-0.5 text-sm font-medium">Audiência</span></h3>
                            <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Agendado para 14 de Junho, 2024 | 14:30</time>
                            <p class="mb-4 text-base font-normal text-gray-500 dark:text-gray-400">Aqui irá a descrição: É o serviço que possibilita ao advogado ou ao contador requerer audiência com Procurador da Fazenda
                                Nacional para obter esclarecimentos sobre apenas um caso concreto, referente à inscrição em Dívida Ativa, negociações, requerimento administrativo ou processo judicial, no qual atua
                                representando o contribuinte perante a Fazenda Nacional.</p>
                            <div class="flex -space-x-4 rtl:space-x-reverse">
                                <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ url('/storage/professionals') }}/01J03QJ12G9C17VSZ2M3EQDZV9.jpg" alt="">
                                <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ url('/storage/professionals') }}/01J03QJ12G9C17VSZ2M3EQDZV9.jpg" alt="">
                                <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ url('/storage/professionals') }}/01J03QJ12G9C17VSZ2M3EQDZV9.jpg" alt="">
                                <img class="h-10 w-10 rounded-full border-2 border-white dark:border-gray-800" src="{{ url('/storage/professionals') }}/01J03QJ12G9C17VSZ2M3EQDZV9.jpg" alt="">
                            </div>
                        </li>
                        <li class="mb-10 ms-6">
                            <span class="ring-3 bg-primary-100 dark:bg-primary-900 absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full ring-white dark:ring-gray-900">
                                <svg class="text-primary-800 dark:text-primary-300 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </span>
                            <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Evento 02</h3>
                            <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Agendado para 16 de Junho, 2024 | 14:30</time>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">Aqui terá a descrição do evento.</p>
                        </li>
                        <li class="mb-10 ms-6">
                            <span class="ring-3 bg-primary-100 dark:bg-primary-900 absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full ring-white dark:ring-gray-900">
                                <svg class="text-primary-800 dark:text-primary-300 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </span>
                            <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Evento 03</h3>
                            <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Agendado para 16 de Junho, 2024 | 14:30</time>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">Aqui terá a descrição do evento.</p>
                        </li>
                        <li class="mb-10 ms-6">
                            <span class="ring-3 bg-primary-100 dark:bg-primary-900 absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full ring-white dark:ring-gray-900">
                                <svg class="text-primary-800 dark:text-primary-300 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </span>
                            <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Evento 03</h3>
                            <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Agendado para 16 de Junho, 2024 | 14:30</time>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">Aqui terá a descrição do evento.</p>
                        </li>
                        <li class="mb-10 ms-6">
                            <span class="ring-3 bg-primary-100 dark:bg-primary-900 absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full ring-white dark:ring-gray-900">
                                <svg class="text-primary-800 dark:text-primary-300 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </span>
                            <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Evento 03</h3>
                            <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Agendado para 16 de Junho, 2024 | 14:30</time>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">Aqui terá a descrição do evento.</p>
                        </li>
                        <li class="mb-10 ms-6">
                            <span class="ring-3 bg-primary-100 dark:bg-primary-900 absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full ring-white dark:ring-gray-900">
                                <svg class="text-primary-800 dark:text-primary-300 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </span>
                            <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Evento 03</h3>
                            <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Agendado para 16 de Junho, 2024 | 14:30</time>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">Aqui terá a descrição do evento.</p>
                        </li>
                        <li class="mb-10 ms-6">
                            <span class="ring-3 bg-primary-100 dark:bg-primary-900 absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full ring-white dark:ring-gray-900">
                                <svg class="text-primary-800 dark:text-primary-300 h-2.5 w-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </span>
                            <h3 class="mb-1 text-lg font-semibold text-gray-900 dark:text-white">Evento 03</h3>
                            <time class="mb-2 block text-sm font-normal leading-none text-gray-400 dark:text-gray-500">Agendado para 16 de Junho, 2024 | 14:30</time>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">Aqui terá a descrição do evento.</p>
                        </li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
</x-filament-panels::page>
