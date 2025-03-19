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

    <div x-data="{ openModal: false, selectedBillingId: null }">
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
                        <!-- Open Modal and Pass Billing ID -->
                        <x-button.primary @click="selectedBillingId = {{ $billing->id }}; openModal = true" class="px-4 py-2 bg-black-600 text-white rounded" text="Paid" />
                    </td>
                </tr>
            @endforeach
        </x-table.table>

        <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-sm">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Enter OR Number</h2>
        
        <form x-bind:action="'{{ route('view.pending.markAsPaid', '') }}/' + selectedBillingId" method="POST">
            @csrf
            <input type="number" name="or_number" placeholder="Enter OR Number" 
                   class="border border-gray-300 rounded-lg p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" />
            
            <div class="mt-6 flex gap-3">
                <x-button.primary   
                    type="button" @click="openModal = false" text="Cancel" />
                
                <x-button.primary 
                    type="submit" text="Paid" />
            </div>
        </form>
    </div>
</div>

    </div>
@endsection
