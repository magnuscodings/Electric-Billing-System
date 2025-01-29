@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="{{ route('view.meters') }}" title="Meter Code" subtitle=" {{ $meter->meterCode }}" />

    <x-table.table :headers="['Reading', 'Date of Reading', 'Consumption', 'Rate', 'Total Amount', 'Billing Date', 'Client']">
        @foreach ($readings as $reading)
            <tr>
                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ number_format($reading->reading, 0) }}
                    </div>
                </td>
                <x-table-cell :value="$reading->created_at->format('Y-m-d')" unavailable-text="Unavailable" />
                <x-table-cell :value="number_format($reading->consumption, 0)" unavailable-text="0.00" />
                <x-table-cell :value="number_format($reading->billing?->rate, 0)" unavailable-text="Unavailable" />
                <x-table-cell :value="number_format($reading->billing?->totalAmount ?? 0, 2)" unavailable-text="Unavailable" />
                <x-table-cell :value="optional($reading->billing)->billingDate?->format('Y-m-d')" unavailable-text="Unavailable" />
                <x-table-cell :value="optional($reading->billing)->client?->fullName" unavailable-text="Unavailable"
                    isDeleted="{{ $reading->billing?->client?->trashed() }}" />
            </tr>
        @endforeach
    </x-table.table>
@endsection
