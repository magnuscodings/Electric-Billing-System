@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="#" title="Incoming Billing Requests" />

    @if (session('success'))
        <x-alert.success info="{{ session('meterCode') }}" message="{{ session('success') }}" />
    @endif

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <div class="flex items-center">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="font-medium">Please fix the following errors:</span>
            </div>
            <ul class="mt-1.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-table.table :headers="['Meter Code', 'Current Reading', 'Previous Reading', 'Consumption', 'Reading Date', 'Actions']">
        @foreach ($meters as $meter)
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $meter->meterCode }}
                    </div>
                </td>

                <x-table-cell :value="$meter->latestReading?->reading" unavailable-text="Unavailable" />
                <x-table-cell :value="$meter->previousReading?->reading" unavailable-text="First Reading" />
                <x-table-cell :value="$meter->latestReading?->consumption" unavailable-text="Unavailable" />
                <x-table-cell :value="$meter->latestReading?->created_at" unavailable-text="Unavailable" />

                <td class="p-4 space-x-2 whitespace-nowrap">
                    {{-- route('clients.view', $client['id']) --}}
                    <x-button.primary text="Create Billing" data-drawer-target="drawer-create-billing-default"
                        data-drawer-show="drawer-create-billing-default"
                        data-meter-reading-id="{{ $meter->latestReading->id }}"
                        aria-controls="drawer-create-billing-default" data-drawer-placement="right" />
                </td>
            </tr>
        @endforeach
    </x-table.table>
    {{-- <x-table.pagination :paginator="$meters" /> --}}

    {{-- Create Billing Request --}}
    <div id="drawer-create-billing-default"
        class="fixed top-0 right-0 z-40 w-full h-screen max-w-xs p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800"
        tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">

        <h5 id="drawer-label"
            class="inline-flex items-center mb-6 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
            New Billing</h5>
        <button type="button" data-drawer-dismiss="drawer-create-billing-default"
            aria-controls="drawer-create-billing-default"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <x-lucide-x class="w-5 h-5" />
            <span class="sr-only">Close menu</span>
        </button>
        <form id="billingForm" action="{{ route('view.billing.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="meterReadingId" id="meterReadingId">

            <x-input-field id="meterCode" name="meterCode" label="Meter code" type="text" :value="old('meterCode')"
                :error-message="$errors->first('meterCode')" placeholder="Type meter code" required readonly />

            <x-input-field id="currentReading" name="currentReading" label="Current reading" type="number"
                :value="old('currentReading')" :error-message="$errors->first('currentReading')" placeholder="Type current reading" required readonly />

            <x-input-field id="previousReading" name="previousReading" label="Previous reading" type="number"
                :value="old('previousReading')" :error-message="$errors->first('previousReading')" placeholder="Type previous reading" required readonly />

            <x-input-field id="consumption" name="consumption" label="Consumption (kWh)" type="number" :value="old('consumption')"
                :error-message="$errors->first('consumption')" placeholder="Type previous reading" required readonly />

            <x-input-field id="rate" name="rate" label="Rate per kWh" type="number" step="0.01"
                :value="$currentRate" :error-message="$errors->first('rate')" placeholder="Type rate" required />

            <x-input-field id="totalAmount" name="totalAmount" label="Total Amount" type="number" :value="old('totalAmount')"
                :error-message="$errors->first('totalAmount')" placeholder="Type total amount" required readonly />

            <x-input-field id="billingDate" name="billingDate" label="Due Date" type="date" :value="now()->setDay(15)->format('Y-m-d')"
                :error-message="$errors->first('billingDate')" placeholder="Type total amount" required />

            <div class="bottom-0 left-0 flex justify-center w-full pb-4 space-x-4 md:px-4 md:absolute">
                <button type="submit"
                    class="text-white w-full justify-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    Add Billing
                </button>
                <button type="button" data-drawer-dismiss="drawer-create-billing-default"
                    aria-controls="drawer-create-billing-default"
                    class="inline-flex w-full justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    <x-lucide-x class="w-5 h-5 -ml-1 sm:mr-1" />
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Get all "Create Billing" buttons
            const createBillingButtons = document.querySelectorAll(
                '[data-drawer-target="drawer-create-billing-default"]');

            createBillingButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const meterCode = row.querySelector('td:first-child div').textContent.trim();
                    const currentReading = row.querySelector('td:nth-child(2)').textContent.trim();
                    const previousReading = row.querySelector('td:nth-child(3)').textContent.trim();
                    const consumption = row.querySelector('td:nth-child(4)').textContent.trim();
                    const meterReadingId = this.dataset.meterReadingId;

                    // Set the values in the form
                    document.getElementById('meterCode').value = meterCode;
                    document.getElementById('previousReading').value = previousReading !==
                        'First Reading' ? previousReading : '0';
                    document.getElementById('currentReading').value = currentReading;
                    document.getElementById('consumption').value = consumption;
                    document.getElementById('meterReadingId').value = meterReadingId;

                    // Calculate initial total amount
                    calculateTotal();
                });
            });

            // Calculate total amount when rate changes
            const rateInput = document.getElementById('rate');
            const consumptionInput = document.getElementById('consumption');
            const totalAmountInput = document.getElementById('totalAmount');

            function calculateTotal() {
                const consumption = parseFloat(consumptionInput.value) || 0;
                const rate = parseFloat(rateInput.value) || 0;
                let total = consumption * rate;

                if (consumption == 0) {
                    total = rate;
                }

                totalAmountInput.value = total.toFixed(2);
            }

            rateInput.addEventListener('input', calculateTotal);

            // Form validation
            const form = document.getElementById('billingForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to create this billing?')) {
                    this.submit();
                }
            });
        });
    </script>
@endsection
