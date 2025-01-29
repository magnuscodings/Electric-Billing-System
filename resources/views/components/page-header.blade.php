<div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full">
        <div>
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href={{ $homeUrl }}
                            class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                            <x-lucide-house class="w-5 h-5 mr-2.5" />
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500"
                                aria-current="page">{{ $title }} page</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <div class="flex flex-col flex-grow">
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </div>
                <div class="text-base font-normal text-gray-500 dark:text-gray-400">
                    {{ $subtitle ?? '' }}
                </div>
            </div>
        </div>
    </div>
</div>
