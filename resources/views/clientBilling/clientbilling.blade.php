@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="{{ route('view.clients') }}" title="Client" subtitle="{{ $client->fullName }}" />

    @if (session('error'))
        <x-alert.error message="{{ session('error') }}" />
    @endif

    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 w-full">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="flex flex-col flex-grow">
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                        Stall Number
                    </div>
                    <div class="text-base font-normal text-gray-500 dark:text-gray-400">
                        {{ $client->stallNumber }}
                    </div>
                </div>
                <div class="flex flex-col flex-grow">
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                        Address
                    </div>
                    <div class="text-base font-normal text-gray-500 dark:text-gray-400">
                        {{ $client->address }}
                    </div>
                </div>
            </div>
            <div class="flex flex-row-reverse mt-auto">
                <a href="{{ route('view.client.report', ['id' => $client->id]) }}" target="_blank"
                    class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">
                    Print Report
                </a>
            </div>
        </div>
    </div>


    <x-table.table :headers="[
        'Meter Code',
        'Reading',
        'Date of Reading',
        'Consumption',
        'Rate',
        'Total Amount',
        'Billing Date',
        'Or Number',
        'Status',
    ]">
        @foreach ($client->billings as $bill)
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $bill->meterReading->meter->meterCode }}
                    </div>
                    @if ($bill->meterReading->trashed())
                        <div>
                            <span
                                class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Deleted</span>
                        </div>
                    @endif
                </td>
                <x-table-cell :value="$bill->meterReading->reading" unavailable-text="Unavailable" />
                <x-table-cell :value="$bill->meterReading->created_at->format('m-d-Y')" unavailable-text="Unavailable" />
                <x-table-cell :value="$bill->meterReading->consumption" unavailable-text="Unavailable" />
                <x-table-cell :value="$bill->rate" unavailable-text="Unavailable" />
                <x-table-cell :value="$bill->totalAmount" unavailable-text="Unavailable" />
                <x-table-cell :value="$bill->billingDate->format('m-d-Y')" unavailable-text="Unavailable" />
                <x-table-cell :value="$bill->or_number" unavailable-text="Unavailable" />
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    @if ($bill->status === 0)
                        <span
                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Unpaid</span>
                    @else
                        <span
                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">Paid</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </x-table.table>
@endsection
