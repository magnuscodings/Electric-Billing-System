{{-- resources/views/components/table-cell.blade.php --}}

<td class="p-4 text-base font-medium whitespace-nowrap {{ $additionalClasses }}">
    @if ($value)
        <span class="text-gray-900 dark:text-white">
            {{ $value }}
            @if ($isDeleted)
                <div>
                    <span
                        class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">Deleted</span>
                </div>
            @endif
        </span>
    @else
        <span class="text-red-500 dark:text-red-400">
            {{ $unavailableText }}
        </span>
    @endif
</td>
