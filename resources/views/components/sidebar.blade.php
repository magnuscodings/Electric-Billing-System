<aside id="sidebar"
    class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 hidden w-64 h-full pt-16 font-normal duration-75 lg:flex transition-width"
    aria-label="Sidebar">

    <div
        class="relative flex flex-col flex-1 min-h-0 pt-0 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
            <div class="flex-1 px-3 space-y-1 bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                <ul class="pb-2 space-y-2">
                    <li>
                    <a href="{{ route('view.dashboard') }}"
                            class="flex items-center p-2 text-base text-gray-900 rounded-lg transition-colors duration-200 hover:bg-blue-100 group dark:text-gray-200 dark:hover:bg-blue-700 
                            {{ request()->routeIs('view.dashboard') ? 'bg-blue-100 dark:bg-blue-700' : '' }}">
                            <x-lucide-layout-dashboard
                                class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                            <span class="ml-3" sidebar-toggle-item>Dashboard</span>
                        </a>
                    </li>
                </ul>
                <ul class="pt-2 pb-2 space-y-2">

                    <li>
                    <a href="{{ route('view.clients') }}"
                            class="flex items-center p-2 text-base text-gray-900 rounded-lg transition-colors duration-200 hover:bg-blue-100 group dark:text-gray-200 dark:hover:bg-blue-700 
                            {{ request()->routeIs('view.clients') ? 'bg-blue-100 dark:bg-blue-700' : '' }}">
                            <x-lucide-users
                                class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                            <span class="ml-3" sidebar-toggle-item>Clients</span>
                        </a>
                    </li>

                    <li>
                    <a href="{{ route('view.meters') }}"
                            class="flex items-center p-2 text-base text-gray-900 rounded-lg transition-colors duration-200 hover:bg-blue-100 group dark:text-gray-200 dark:hover:bg-blue-700 
                            {{ request()->routeIs('view.meters') ? 'bg-blue-100 dark:bg-blue-700' : '' }}">
                            <x-lucide-circle-gauge
                                class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                            <span class="ml-3" sidebar-toggle-item>Meter codes</span>
                        </a>
                    </li>

                    <li>
                    <a href="{{ route('view.billings') }}"
                            class="flex items-center p-2 text-base text-gray-900 rounded-lg transition-colors duration-200 hover:bg-blue-100 group dark:text-gray-200 dark:hover:bg-blue-700 relative
                            {{ request()->routeIs('view.billings') ? 'bg-blue-100 dark:bg-blue-700' : '' }}">
                            <x-lucide-list-checks
                                class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                            <span class="ml-3" sidebar-toggle-item>Billing Requests</span>

                            <span id="new-meter-reading-badge" data-test-id="new-meter-reading-badge"
                                class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full -mt-1 -mr-1 hidden">
                                0
                            </span>
                        </a>
                    </li>

                    <li>
                    <a href="{{ route('view.pendings') }}"
                            class="flex items-center p-2 text-base text-gray-900 rounded-lg transition-colors duration-200 hover:bg-blue-100 group dark:text-gray-200 dark:hover:bg-blue-700
                            {{ request()->routeIs('view.pendings') ? 'bg-blue-100 dark:bg-blue-700' : '' }}">
                            <x-lucide-receipt-text
                                class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                            <span class="ml-3" sidebar-toggle-item>Pending Billings</span>
                        </a>
                    </li>

                </ul>
                <ul class="pt-2 space-y-2">
                    <li>
                    <a href="{{ route('view.reports') }}"
                            class="flex items-center p-2 text-base text-gray-900 rounded-lg transition-colors duration-200 hover:bg-blue-100 group dark:text-gray-200 dark:hover:bg-blue-700
                            {{ request()->routeIs('view.reports') ? 'bg-blue-100 dark:bg-blue-700' : '' }}">
                            <x-lucide-file-text
                                class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                            <span class="ml-3" sidebar-toggle-item>Reports</span>
                        </a>
                    </li>

                    <li>
                    <a href="{{ route('view.accounts') }}"
                            class="flex items-center p-2 text-base text-gray-900 rounded-lg transition-colors duration-200 hover:bg-blue-100 group dark:text-gray-200 dark:hover:bg-blue-700
                            {{ request()->routeIs('view.accounts') ? 'bg-blue-100 dark:bg-blue-700' : '' }}">
                            <x-lucide-user-round
                                class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" />
                            <span class="ml-3" sidebar-toggle-item>Accounts</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Echo is defined
            if (typeof Echo === 'undefined') {
                console.error('Laravel Echo not loaded');
                return;
            }

            // Function to update the badge
            function updateBadge(count) {
                const badge = document.getElementById('new-meter-reading-badge');
                if (badge) {
                    // Only increment if the current count is 0 or less
                    const currentCount = parseInt(badge.textContent) || 0;
                    const newCount = currentCount + count;

                    if (newCount > 0) {
                        badge.textContent = newCount;
                        badge.classList.remove('hidden');
                    }
                }
            }

            // Function to refresh the billings page content if on the billings page
            function refreshBillingsPage() {
                // Check if we're on the billings page
                if (window.location.pathname === "{{ route('view.billings', [], false) }}") {
                    // Use AJAX to refresh the table content
                    fetch("{{ route('view.billings') }}")
                        .then(response => response.text())
                        .then(html => {
                            // Parse the new HTML and update only the table
                            const parser = new DOMParser();
                            const newDoc = parser.parseFromString(html, 'text/html');
                            const newTable = newDoc.querySelector('table');

                            if (newTable) {
                                const existingTable = document.querySelector('table');
                                if (existingTable) {
                                    existingTable.innerHTML = newTable.innerHTML;
                                }
                            }
                        });
                }
            }

            Pusher.logToConsole = true;

            var pusher = new Pusher('6d8a381178eee39b7a05', {
                cluster: 'ap1'
            });

            var channel = pusher.subscribe('meter-readings');
            channel.bind('meter-reading-created', function(data) {
                updateBadge(1);
                // Refresh billings page if needed
                refreshBillingsPage();
            });
        });
    </script>
@endpush
