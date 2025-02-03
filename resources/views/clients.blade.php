{{-- resources/views/clients.blade.php --}}
@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="#" title="Clients" />

    @if (session('success'))
        <x-alert.success info="{{ session('client')['fullName'] }}" message="{{ session('success') }}" />
    @endif

    @if (session('error'))
        <x-alert.error message="{{ session('error') }}" />
    @endif

    <div
        class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full">
            <div>
                <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="{{ route('view.client.search') }}" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <input type="text" name="email" id="products-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Search for clients">
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
                        @if (request('email'))
                            <div class="ml-2">
                                <a href="{{ route('view.clients') }}"
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
                    <x-button.primary id="createClientButton" text="Add new client"
                        data-drawer-target="drawer-create-client-default" data-drawer-show="drawer-create-client-default"
                        aria-controls="drawer-create-client-default" data-drawer-placement="right" />
                </div>
            </div>
        </div>
    </div>

    <x-table.table :headers="['Name', 'Email', 'Address', 'Meter Code', 'Stall Number', 'Actions']">
        @foreach ($clients as $client)
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                    <div class="text-base font-semibold text-gray-900 dark:text-white capitalize">
                        {{ $client['fullName'] }}
                    </div>
                </td>
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white capitalize">
                    {{ $client['email'] }}
                </td>
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white capitalize">
                    {{ $client['address'] }}
                </td>
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $client->meter->meterCode ?? 'Unavailable' }}
                </td>
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $client->meter->stallNumber ?? 'Unavailable' }}
                </td>
                <td class="p-4 space-x-2 whitespace-nowrap">
                    {{-- route('clients.view', $client['id']) --}}
                    <x-button.secondary text="View Billing" href="{{ route('view.client.billings', $client->id) }}" />
                    <x-button.primary id="updateClientButton" text="Update" data-action="Update"
                        data-id="{{ $client->id }}" data-drawer-target="drawer-update-client-default"
                        data-drawer-show="drawer-update-client-default" aria-controls="drawer-update-client-default"
                        data-drawer-placement="right" />
                    <x-button.danger text="Delete" data-id="{{ $client->id }}" data-modal-target="popup-modal"
                        data-modal-toggle="popup-modal" />
                </td>
            </tr>
        @endforeach
    </x-table.table>
    <x-table.pagination :paginator="$clients" />

    {{-- Add Client Drawer component remains the same --}}
    <div id="drawer-create-client-default"
        class="fixed top-0 right-0 z-40 w-full h-screen max-w-xs p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800"
        tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">

        <h5 id="drawer-label"
            class="inline-flex items-center mb-6 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
            New Client</h5>
        <button type="button" data-drawer-dismiss="drawer-create-client-default"
            aria-controls="drawer-create-client-default"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close menu</span>
        </button>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
                @foreach ($errors->all() as $error)
                    <div class="mt-1">• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form id="addClientForm" class="flex h-[calc(100%-8rem)] flex-col gap-3 overflow-y-auto">
            @csrf

            <!-- Error Alert Container -->
            <div id="errorAlert"
                class="hidden p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
            </div>

            <!-- Success Alert Container -->
            <div id="successAlert"
                class="hidden p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                role="alert">
            </div>

            <x-input-field id="firstName" name="firstName" label="First Name" type="text" :value="old('firstName')"
                :error-message="$errors->first('firstName')" placeholder="Type first name" />

            <x-input-field id="middleName" name="middleName" type="text" :value="old('middleName')" :error-message="$errors->first('middleName')"
                label="Middle Name" placeholder="Type middle name" />

            <x-input-field id="lastName" name="lastName" type="text" :value="old('lastName')" :error-message="$errors->first('lastName')"
                label="Last Name" placeholder="Type last name" />

            <x-input-field id="suffix" name="suffix" type="text" :value="old('suffix')" :error-message="$errors->first('suffix')" label="Suffix"
                placeholder="Type suffix" />

            <x-input-field id="email" name="email" type="email" :value="old('email')" :error-message="$errors->first('email')"
                label="Email" placeholder="Type email" />

                <div>
                
                <label for="barangay" class="block mt-4 mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Barangay</label>
                <select id="barangay" name="barangay"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @includeIf('barangay')
                </select>
            </div>

            <x-textarea-field id="address" name="address" label="Address" :value="old('address')" :error-message="$errors->first('address')"
                placeholder="Enter address" :rows="6" :max-length="500" :show-character-count="true" />

            <div>
                
                <label for="meterCode" class="block mt-4 mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a Stall Number</label>
                <select id="meterCode" name="meterCode"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    @foreach ($meters as $meter)
                        @if (is_null($meter->clientId))
                            <option value="{{ $meter->id }}">{{ $meter->stallNumber }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div
                class=" left-0 right-0 border-t border-gray-200 bg-white p-4 dark:border-gray-600 dark:bg-gray-800">
                <div class="flex w-full space-x-3">
                    <button type="submit" id="submitAddClient"
                        class="text-white w-full justify-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        <span class="spinner hidden"></span>
                        <span class="button-text">Add client</span>
                    </button>
                    <button type="button" data-drawer-dismiss="drawer-create-client-default"
                        aria-controls="drawer-create-client-default"
                        class="inline-flex w-full justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        <x-lucide-x class="w-5 h-5 -ml-1 sm:mr-1" />
                        Cancel
                    </button>
                </div>
            </div>
        </form>

    </div>

    <!-- Update Client Drawer -->
    <div id="drawer-update-client-default"
        class="fixed top-0 right-0 z-40 w-full h-screen max-w-xs p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800"
        tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">
        <h5 id="drawer-label"
            class="inline-flex items-center mb-6 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
            Update Client</h5>
        <button type="button" data-drawer-dismiss="drawer-update-client-default"
            aria-controls="drawer-update-client-default"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sr-only">Close menu</span>
        </button>

        <form id="updateClientForm" class="flex h-[calc(100%-8rem)] flex-col gap-3 overflow-y-auto">
            @csrf
            @method('POST')

            <!-- Error Alert Container -->
            <div id="errorAlert"
                class="hidden p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                role="alert">
            </div>

            <!-- Success Alert Container -->
            <div id="successAlert"
                class="hidden p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                role="alert">
            </div>

            <x-input-field id="updateFirstName" name="updateFirstName" label="First Name" type="text"
                :value="old('updateFirstName')" :error-message="$errors->first('updateFirstName')" placeholder="Type first name" required />

            <x-input-field id="updateMiddleName" name="updateMiddleName" type="text" :value="old('updateMiddleName')"
                :error-message="$errors->first('updateMiddleName')" label="Middle Name" placeholder="Type middle name" />

            <x-input-field id="updateLastName" name="updateLastName" type="text" :value="old('updateLastName')" :error-message="$errors->first('updateLastName')"
                label="Last Name" placeholder="Type last name" required />

            <x-input-field id="updateSuffix" name="updateSuffix" type="text" :value="old('updateSuffix')" :error-message="$errors->first('updateSuffix')"
                label="Suffix" placeholder="Type suffix" />

            <x-textarea-field id="updateAddress" name="updateAddress" label="UpdateAddress" :value="old('updateAddress')"
                :error-message="$errors->first('updateAddress')" placeholder="Enter address" :rows="6" :max-length="500" :show-character-count="true"
                required />

            <div>
                <label for="updateMeterCode" class="block mt-4 mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a
                    Stall Number</label>
                <select id="updateMeterCode" name="updateMeterCode"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    @foreach ($meters as $meter)
                        @if (is_null($meter->clientId))
                            <option value="{{ $meter->id }}">{{ $meter->stallNumber }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

          

            <div
                class="left-0 right-0 border-t border-gray-200 bg-white p-4 dark:border-gray-600 dark:bg-gray-800">
                <div class="flex w-full space-x-3">
                    <button type="submit" id="submitUpdateClient"
                        class="text-white w-full justify-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        <span class="spinner"></span>
                        <span class="button-text">Update client</span>
                    </button>
                    <button type="button" data-drawer-dismiss="drawer-update-client-default"
                        aria-controls="drawer-update-client-default"
                        class="inline-flex w-full justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        <x-lucide-x class="w-5 h-5 -ml-1 sm:mr-1" />
                        Cancel
                    </button>
                </div>
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
                        this client?</h3>
                    <h3 class="mb-5 text-sm font-normal text-gray-500 dark:text-gray-400">Deleting means remove the link to
                        the meter code also.</h3>
                    <form id="deleteForm" method="POST" action="{{ route('view.client.destroy', 'id') }}">
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

    @push('scripts')
        <script>
            document.getElementById('addClientForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                // Reset alerts
                document.getElementById('errorAlert').classList.add('hidden');
                document.getElementById('successAlert').classList.add('hidden');

                // Show loading spinner
                const submitButton = document.getElementById('submitAddClient');
                submitButton.querySelector('.spinner').classList.remove('hidden');
                submitButton.querySelector('.button-text').classList.add('hidden');
                submitButton.disabled = true;

                try {
                    const formData = new FormData(this);
                    const response = await fetch('{{ route('view.client.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw result;
                    }

                    // Show success message
                    const successAlert = document.getElementById('successAlert');
                    successAlert.innerHTML = `
                        <h5 class="alert-heading">Account Created Successfully!</h5>
                        <hr>
                        <p><strong>Pattern:</strong> ${result.login_credentials.pattern_explanation}</p>
                        <p><strong>Login Credentials:</strong></p>
                        <ul>
                            <li>Email: ${result.login_credentials.email}</li>
                            <li>Password: ${result.login_credentials.password}</li>
                        </ul>
                        <hr>
                        <p class="mb-0">Please provide these credentials to the client.</p>
                    `;
                    successAlert.classList.remove('hidden');

                    // Reset form
                    this.reset();

                    // Refresh the clients list or update UI as needed
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);

                } catch (error) {
                    // Show error message
                    const errorAlert = document.getElementById('errorAlert');
                    if (error.errors) {
                        // Validation errors
                        errorAlert.innerHTML = Object.values(error.errors)
                            .flat()
                            .map(err => `<div class="mt-1">• ${err}</div>`)
                            .join('');
                    } else {
                        // General error
                        errorAlert.innerHTML = `<div class="mt-1">• ${error.message || 'An error occurred'}</div>`;
                    }
                    errorAlert.classList.remove('hidden');
                } finally {
                    // Hide loading spinner
                    submitButton.querySelector('.spinner').classList.add('hidden');
                    submitButton.querySelector('.button-text').classList.remove('hidden');
                    submitButton.disabled = false;
                }
            });

            // Delete functionality
            document.addEventListener('DOMContentLoaded', function() {
                // Delete functionality
                const deleteModal = document.getElementById('popup-modal');
                const deleteForm = document.getElementById('deleteForm');
                let clientIdToDelete;

                // Handle delete button clicks
                document.querySelectorAll('[data-modal-target="popup-modal"]').forEach(button => {
                    button.addEventListener('click', function() {
                        clientIdToDelete = this.dataset.id;
                        deleteForm.action = deleteForm.action.replace(/id$/, clientIdToDelete);
                        deleteModal.classList.remove('hidden');
                        deleteModal.classList.add('flex');
                    });
                });
            });

            // Update button click handler
            document.addEventListener('DOMContentLoaded', function() {
                // Get all update buttons
                const updateButtons = document.querySelectorAll('[data-drawer-target="drawer-update-client-default"]');

                // Add click event listener to each update button
                updateButtons.forEach(button => {
                    button.addEventListener('click', async function() {
                        const clientId = this.getAttribute('data-id');

                        try {
                            // Fetch client data
                            const response = await fetch(`/api/clients/${clientId}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });

                            if (!response.ok) throw new Error('Failed to fetch client data');

                            const client = await response.json();

                            // Populate the update form
                            document.getElementById('updateFirstName').value = client.firstName ||
                                '';
                            document.getElementById('updateMiddleName').value = client.middleName ||
                                '';
                            document.getElementById('updateLastName').value = client.lastName || '';
                            document.getElementById('updateSuffix').value = client.suffix || '';
                            document.getElementById('updateAddress').value = client.address || '';
                       
                            // Set the meter code if available
                            const meterSelect = document.getElementById('updateMeterCode');
                            if (client.meter && client.meter.id) {
                                // First, add the current meter as an option if it doesn't exist
                                let exists = false;
                                for (let option of meterSelect.options) {
                                    if (option.value == client.meter.id) {
                                        exists = true;
                                        break;
                                    }
                                }
                                if (!exists) {
                                    const option = new Option(client.meter.stallNumber, client.meter
                                        .id);
                                    meterSelect.add(option);
                                }
                                meterSelect.value = client.meter.id;
                            }

                            // Set the form's data-client-id attribute for submission
                            const updateForm = document.getElementById('updateClientForm');
                            updateForm.setAttribute('data-client-id', clientId);

                        } catch (error) {
                            console.error('Error fetching client data:', error);
                            // Show error message to user
                            const errorAlert = document.querySelector(
                                '#drawer-update-client-default #errorAlert');
                            errorAlert.innerHTML =
                                `<div class="mt-1">• Failed to load client data: ${error.message}</div>`;
                            errorAlert.classList.remove('hidden');
                        }
                    });
                });

                // Handle form submission
                const updateForm = document.getElementById('updateClientForm');
                updateForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const clientId = this.getAttribute('data-client-id');
                    if (!clientId) {
                        console.error('No client ID found');
                        return;
                    }

                    // Reset alerts
                    const errorAlert = this.querySelector('#errorAlert');
                    const successAlert = this.querySelector('#successAlert');
                    errorAlert.classList.add('hidden');
                    successAlert.classList.add('hidden');

                    // Show loading spinner
                    const submitButton = document.getElementById('submitUpdateClient');
                    submitButton.querySelector('.spinner').classList.remove('hidden');
                    submitButton.querySelector('.button-text').classList.add('hidden');
                    submitButton.disabled = true;

                    try {
                        const formData = new FormData(this);
                        const response = await fetch(`/view/client/${clientId}/update`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw result;
                        }

                        // Show success message
                        successAlert.innerHTML = `
                <div class="flex p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Success</span>
                    <div>Client updated successfully!</div>
                </div>
            `;
                        successAlert.classList.remove('hidden');

                        // Refresh the page after 2 seconds
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);

                    } catch (error) {
                        // Show error message
                        let errorMessage = '';
                        if (error.errors) {
                            // Validation errors
                            errorMessage = Object.values(error.errors)
                                .flat()
                                .map(err => `<div class="mt-1">• ${err}</div>`)
                                .join('');
                        } else {
                            // General error
                            errorMessage =
                                `<div class="mt-1">• ${error.message || 'An error occurred'}</div>`;
                        }

                        errorAlert.innerHTML = `
                <div class="flex p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Error</span>
                    <div>${errorMessage}</div>
                </div>
            `;
                        errorAlert.classList.remove('hidden');
                    } finally {
                        // Hide loading spinner
                        submitButton.querySelector('.spinner').classList.add('hidden');
                        submitButton.querySelector('.button-text').classList.remove('hidden');
                        submitButton.disabled = false;
                    }
                });
            });
        </script>
    @endpush

@endsection
