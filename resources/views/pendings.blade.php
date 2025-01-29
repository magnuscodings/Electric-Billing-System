@extends('layout.layout')
@section('content')
    <x-page-header homeUrl="#" title="Pending Billings" />

    @if (session('success'))
        <x-alert.success info="" message="{{ session('success') }}" />
    @endif

    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <ul class="mt-1.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-table.table :headers="[
        'Client Name',
        'Meter Code',
        'Current Reading',
        'Previous Reading',
        'Consumption',
        'Total Amount',
        'Actions',
    ]">
        @foreach ($unpaidBillings as $billing)
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $billing->meterReading?->meter?->client?->fullName }}
                    </div>
                </td>

                <x-table-cell :value="$billing->meterReading?->meter?->meterCode" unavailable-text="Unavailable" />
                <x-table-cell :value="$billing->meterReading?->reading" unavailable-text="Unavailable" />
                <x-table-cell :value="$billing->meterReading?->meter?->previousReading?->reading" unavailable-text="Unavailable" />
                <x-table-cell :value="$billing->meterReading?->consumption" unavailable-text="Unavailable" />
                <x-table-cell :value="$billing->totalAmount" unavailable-text="Unavailable" />

                <td class="p-4 space-x-2 whitespace-nowrap">
                    <form action="{{ route('view.pending.markAsPaid', $billing->id) }}" method="POST">
                        @csrf
                        <x-button.primary type="submit" text="Paid" />
                    </form>
                </td>
            </tr>
        @endforeach
    </x-table.table>

@endsection
