{{-- resources/views/accounts/index.blade.php --}}
@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="#" title="Account Management" />

    @if (session('success'))
        <x-alert.success info="{{ session('name') }}" message="{{ session('success') }}" />
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
                        <form class="sm:pr-3" action="{{ route('view.accounts') }}" method="GET">
                            <label for="users-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <input type="text" name="search" id="users-search" value="{{ request('search') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Search by name or email">
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

                        @if (request('search'))
                            <div class="ml-2">
                                <a href="{{ route('view.accounts') }}"
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
                    <x-button.primary id="createUserButton" text="Add new user"
                        data-drawer-target="drawer-create-user-default" data-drawer-show="drawer-create-user-default"
                        aria-controls="drawer-create-user-default" data-drawer-placement="right" />
                </div>
            </div>
        </div>
    </div>

    <x-table.table :headers="['Name', 'Email', 'Role', 'Created At', 'Actions']">
        @foreach ($users as $user)
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                    <div class="text-base font-semibold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </div>
                </td>
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->email }}
                </td>
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white capitalize">
                    <span
                        class="px-2 py-1 rounded-full text-sm {{ $user->role->slug === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $user->role->name }}
                    </span>
                </td>
                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $user->created_at->format('M d, Y') }}
                </td>
                <td class="p-4 space-x-2 whitespace-nowrap">

                    @if ($user->role->slug == 'reader')
                        <x-button.primary id="updateUserButton" text="Update" data-action="Update"
                            data-drawer-target="drawer-update-user-default" data-drawer-show="drawer-update-user-default"
                            aria-controls="drawer-update-user-default" data-drawer-placement="right"
                            data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}"
                            data-user-email="{{ $user->email }}" data-user-role="{{ $user->role->slug }}" />
                    @endif

                    @if (auth()->user()->id !== $user->id && $user->id !== 1 && $user->role->slug !== 'client')
                        <x-button.danger text="Delete" data-id="{{ $user->id }}" data-modal-target="popup-modal"
                            data-modal-toggle="popup-modal" />
                    @endif

                </td>
            </tr>
        @endforeach
    </x-table.table>
    <x-table.pagination :paginator="$users" />

    <!-- Create User Drawer -->
    <div id="drawer-create-user-default"
        class="fixed top-0 right-0 z-40 w-full h-screen max-w-xs p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800"
        tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">

        <h5 id="drawer-label"
            class="inline-flex items-center mb-6 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
            New User
        </h5>
        <button type="button" data-drawer-dismiss="drawer-create-user-default"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
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

        <form action="{{ route('view.account') }}" method="POST"
            class="flex h-[calc(100%-8rem)] flex-col gap-3 overflow-y-auto" id="addUserForm">
            @csrf

            <x-input-field id="name" name="name" label="Name" type="text" :value="old('name')" :error-message="$errors->first('name')"
                placeholder="Enter full name" required />

            <x-input-field id="email" name="email" type="email" class="normal-case" :value="old('email')"
                :error-message="$errors->first('email')" label="Email Address" placeholder="Enter email address" required />

            <div>
                <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                    Role</label>
                <select id="role_id" name="role_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required>
                    <option value="">Select a role</option>
                    <option value="1">Admin</option>
                    <option value="2">Reader</option>
                </select>
            </div>

            <x-input-field id="password" name="password" type="password" :error-message="$errors->first('password')" label="Password"
                placeholder="Enter password" required />

            <x-input-field id="password_confirmation" name="password_confirmation" type="password"
                label="Confirm Password" placeholder="Confirm password" required />

            <div
                class="absolute bottom-0 left-0 right-0 border-t border-gray-200 bg-white p-4 dark:border-gray-600 dark:bg-gray-800">
                <div class="flex w-full space-x-3">
                    <button type="submit" id="submitAddUser"
                        class="text-white w-full justify-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        <span class="spinner"></span>
                        <span class="button-text">Add user</span>
                    </button>
                    <button type="button" data-drawer-dismiss="drawer-create-user-default"
                        class="inline-flex w-full justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Update User Drawer -->
    <div id="drawer-update-user-default"
        class="fixed top-0 right-0 z-40 w-full h-screen max-w-xs p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800"
        tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">

        <h5 id="drawer-label"
            class="inline-flex items-center mb-6 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
            Update User
        </h5>
        <button type="button" data-drawer-dismiss="drawer-update-user-default"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
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

        <form action="" method="POST" class="flex h-[calc(100%-8rem)] flex-col gap-3 overflow-y-auto"
            id="updateUserForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" id="update_user_id">

            <x-input-field id="update_name" name="name" label="Name" type="text" :value="old('name')"
                :error-message="$errors->first('name')" placeholder="Enter full name" required />

            <div
                class="absolute bottom-0 left-0 right-0 border-t border-gray-200 bg-white p-4 dark:border-gray-600 dark:bg-gray-800">
                <div class="flex w-full space-x-3">
                    <button type="submit" id="submitUpdateUser"
                        class="text-white w-full justify-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        <span class="spinner"></span>
                        <span class="button-text">Update user</span>
                    </button>
                    <button type="button" data-drawer-dismiss="drawer-update-user-default"
                        class="inline-flex w-full justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Delete User Modal -->
    <div id="popup-modal" tabindex="-1"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="popup-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete
                        this user?</h3>
                    <form id="deleteUserForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                            Yes, I'm sure
                        </button>
                        <button data-modal-hide="popup-modal" type="button"
                            class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            No, cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get all update buttons
                const updateButtons = document.querySelectorAll('[data-drawer-target="drawer-update-user-default"]');
                const updateForm = document.getElementById('updateUserForm');

                updateButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const userId = this.getAttribute('data-user-id');
                        const userName = this.getAttribute('data-user-name');

                        // Set form action URL
                        updateForm.action = `/accounts/${userId}`;

                        // Populate form fields
                        document.getElementById('update_user_id').value = userId;
                        document.getElementById('update_name').value = userName;
                    });
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get all delete buttons
                const deleteButtons = document.querySelectorAll('[data-modal-target="popup-modal"]');
                const deleteForm = document.getElementById('deleteUserForm');

                deleteButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const userId = this.getAttribute('data-id');

                        // Set form action URL
                        deleteForm.action = `/accounts/${userId}`;
                    });
                });
            });
        </script>
    @endpush
@endsection
