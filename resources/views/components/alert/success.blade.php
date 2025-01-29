@props([
    'info' => '',
    'message' => '',
])

<div class="p-4 text-sm text-green-800 bg-green-50 dark:bg-gray-800 dark:text-green-400 capitalize" role="alert">
    <span class="font-medium capitalize">{{ $info }}</span>
    {{ $message }}
</div>
