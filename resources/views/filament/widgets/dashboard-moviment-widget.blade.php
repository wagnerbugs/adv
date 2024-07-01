@php
use Carbon\Carbon;
@endphp
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="rounded-t-lg overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Processo
                        </th>

                        <th scope="col" class="px-6 py-3 text-center">
                            Data
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="relative overflow-x-auto rounded-b-lg overflow-y-scroll max-h-96">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">

                <tbody>
                    @foreach ($movements as $movement)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white col-span-2">
                            <span class="text-gray-700 whitespace-nowrap dark:text-gray-500">{{ $movement->process }}</span><br>
                            @if (empty($movement->judicial_event))
                            Não informado
                            @else
                            {{ $movement->judicial_event }}
                            @endif

                        </th>
                        <td class="px-6 py-4 text-center">
                            {{ Carbon::parse($movement->event_date)->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>