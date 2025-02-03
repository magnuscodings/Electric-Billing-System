@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="#" title="Meter Codes" />

    @if (session('success'))
        <x-alert.success info="{{ session('meterCode') }}" message="{{ session('success') }}" />
    @endif

    @if (session('error'))
        <x-alert.error info="" message="{{ session('error') }}" />
    @endif

    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full">
            <div>
                <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="{{ route('view.meter.search') }}" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <input type="text" name="meterCode" id="products-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Search for meter">
                                    <button type="submit"
                                        class="absolute right-0 top-0 h-full px-4 text-sm font-medium text-white bg-primary-700 rounded-r-lg border border-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                        </svg>
                                        <span class="sr-only">Search</span>
                                    </button>
                            </div>
                        </form>
                        @if (request('meterCode'))
                            <div class="ml-2">
                                <a href="{{ route('view.meters') }}"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300">
                                    Clear Search
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                    <x-button.primary id="createMeterButton" text="Add new meter"
                        data-drawer-target="drawer-create-meter-default" data-drawer-show="drawer-create-meter-default"
                        aria-controls="drawer-create-meter-default" data-drawer-placement="right" />
                </div>
            </div>
        </div>
    </div>

    <x-table.table :headers="['Meter Code', 'Latest Reading', 'Latest Balance', 'Balance Status', 'Actions']">
        @foreach ($meters as $meter)
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $meter->meterCode }}
                    </div>
                </td>

                <x-table-cell :value="number_format($meter->latestReading?->reading)" unavailable-text="Unavailable" />
                <x-table-cell :value="$meter->latestReading?->billing?->totalAmount" unavailable-text="Unavailable" />
                <x-table-cell :value="$meter->latestReading?->billing?->getStatusLabelAttribute()" unavailable-text="Unavailable" />

                <td class="p-4 space-x-2 whitespace-nowrap">
                    <x-button.secondary text="View Billings" href="{{ route('view.meter.show', $meter->id) }}" />
                    <x-button.danger text="Delete" data-id="{{ $meter->id }}" data-modal-target="popup-modal"
                        data-modal-toggle="popup-modal" />
                </td>
            </tr>
        @endforeach
    </x-table.table>
    <x-table.pagination :paginator="$meters" />

    {{-- Add Drawer component remains the same --}}
    <div id="drawer-create-meter-default"
        class="fixed top-0 right-0 z-40 w-full h-screen max-w-xs p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800"
        tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">

        <h5 id="drawer-label"
            class="inline-flex items-center mb-6 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
            New Meter</h5>
        <button type="button" data-drawer-dismiss="drawer-create-meter-default" aria-controls="drawer-create-meter-default"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <x-lucide-x class="w-5 h-5" />
            <span class="sr-only">Close menu</span>
        </button>

        <form action="{{ route('view.meter.store') }}" method="POST" class="space-y-3" id="createMeterForm">
            @csrf

            <div class="mb-4">
                <x-input-field id="meterCode" name="meterCode" label="Meter code" type="text" :value="old('meterCode')"
                    :error-message="$errors->first('meterCode')" placeholder="Type meter code" required />
                <!-- Error message container -->
                <div id="meterCode-error" class="text-red-500 text-sm mt-1"></div>
            </div>

            <div class="mb-4">
                <x-input-field id="reading" name="reading" label="Reading" type="number" :value="old('reading')"
                    :error-message="$errors->first('reading')" placeholder="Type reading" required />
                <div id="reading-error" class="text-red-500 text-sm mt-1"></div>
            </div>

            <div class="mb-4">
                <x-input-field id="consumption" name="consumption" label="Consumption" type="number" :value="old('consumption')"
                    :error-message="$errors->first('consumption')" placeholder="Type consumption" required />
                <div id="consumption-error" class="text-red-500 text-sm mt-1"></div>
            </div>

            <div class="mb-4">
                <x-input-field id="stallNumber" name="stallNumber" label="Stall Number" type="text" :value="old('stallNumber')"
                    :error-message="$errors->first('stallNumber')" placeholder="Type stallNumber" required />
                <div id="stallNumber-error" class="text-red-500 text-sm mt-1"></div>
            </div>
            
            <!-- General error message container -->
            <div id="general-error" class="text-red-500 text-sm mt-1"></div>

            <div class="bottom-0 left-0 flex justify-center w-full pb-4 space-x-4 md:px-4 md:absolute">
                <button type="submit"
                    class="text-white w-full justify-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    Add meter
                </button>
                <button type="button" data-drawer-dismiss="drawer-create-meter-default"
                    aria-controls="drawer-create-meter-default"
                    class="inline-flex w-full justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                    <x-lucide-x class="w-5 h-5 -ml-1 sm:mr-1" />
                    Cancel
                </button>
            </div>
        </form>
    </div>

    {{-- Delete model --}}
    <div id="popup-modal" tabindex="-1"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="p-4 md:p-5 text-center">
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete
                        this meter code?</h3>
                    <form id="deleteForm" method="POST" action="{{ route('view.meter.destroy', 'id') }}">
                        @csrf
                        @method('DELETE')
                        <button data-modal-hide="popup-modal" type="submit"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Yes, I'm sure
                        </button>
                        <button data-modal-hide="popup-modal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                            cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // resources/js/meters.js
        document.addEventListener('DOMContentLoaded', function() {
            // Delete functionality
            const deleteButtons = document.querySelectorAll('[data-modal-target="popup-modal"]');
            const deleteForm = document.getElementById('deleteForm');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const meterId = this.getAttribute('data-id');
                    deleteForm.action = deleteForm.action.replace(/\/id$/, `/${meterId}`);
                });
            });

            // Edit functionality
            const editButtons = document.querySelectorAll('[data-drawer-target="drawer-edit-meter-default"]');
            const editForm = document.getElementById('editMeterForm');

            editButtons.forEach(button => {
                button.addEventListener('click', async function() {
                    const meterId = this.getAttribute('data-id');

                    try {
                        const response = await fetch(`/meters/${meterId}/edit`);
                        const data = await response.json();

                        // Populate form fields
                        document.getElementById('edit_meterCode').value = data.meterCode;
                        if (data.latestReading) {
                            document.getElementById('edit_reading').value = data.latestReading
                                .reading;
                            document.getElementById('edit_consumption').value = data
                                .latestReading.consumption;
                        }

                        // Update form action
                        editForm.action = `/meters/${meterId}`;
                    } catch (error) {
                        console.error('Error fetching meter data:', error);
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const meterForm = document.getElementById('createMeterForm');
            const meterCodeInput = document.getElementById('meterCode');

            // Function to show error message
            function showError(field, message) {
                const errorDiv = document.getElementById(`${field}-error`);
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.style.display = 'block';
                }
            }

            // Function to clear error message
            function clearError(field) {
                const errorDiv = document.getElementById(`${field}-error`);
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.style.display = 'none';
                }
            }

            // Function to clear all errors
            function clearAllErrors() {
                ['meterCode', 'reading', 'consumption', 'general','stallNumber'].forEach(field => {
                    clearError(field);
                });
            }

            if (meterForm) {
                meterForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    clearAllErrors();

                    try {
                        const formData = new FormData(this);
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();

                        if (!data.success) {
                            // Handle validation errors
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    showError(field, data.errors[field][0]);
                                });
                            }
                            return;
                        }

                        // Success case
                        const drawer = document.getElementById('drawer-create-meter-default');
                        if (drawer) {
                            const closeButton = drawer.querySelector('[data-drawer-dismiss]');
                            if (closeButton) closeButton.click();
                        }

                        // Show success alert (you can customize this)
                        const successAlert = document.createElement('div');
                        successAlert.className =
                            'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg';
                        successAlert.textContent = 'Meter added successfully!';
                        document.body.appendChild(successAlert);

                        // Remove success alert after 3 seconds
                        setTimeout(() => {
                            successAlert.remove();
                            window.location.reload(); // Optional: reload to show new data
                        }, 3000);

                    } catch (error) {
                        console.error('Error:', error);
                        showError('general', 'An unexpected error occurred. Please try again.');
                    }
                });

                // Real-time meter code validation
                if (meterCodeInput) {
                    let timeout;
                    meterCodeInput.addEventListener('input', function() {
                        clearTimeout(timeout);
                        clearError('meterCode');

                        timeout = setTimeout(async () => {
                            if (this.value.length > 0) {
                                try {
                                    const response = await fetch('/meter/validate-code', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            meterCode: this.value
                                        })
                                    });

                                    const data = await response.json();
                                    if (!data.success) {
                                        showError('meterCode', data.errors.meterCode[0]);
                                    }
                                } catch (error) {
                                    console.error('Error:', error);
                                }
                            }
                        }, 500);
                    });
                }
            }
        });
    </script>
@endsection
