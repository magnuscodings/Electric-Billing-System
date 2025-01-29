@props([
    'type' => 'button',
    'text' => '',
    'icon' => null,
    'href' => null,
    'onclick' => null,
    'additionalClass' => '',
    'disabled' => false,
])

@php
    $baseClasses =
        'inline-flex items-center px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700';
    $disabledClasses = 'opacity-50 cursor-not-allowed';
    $classes = $baseClasses . ' ' . $additionalClass . ($disabled ? ' ' . $disabledClasses : '');
@endphp

@if ($href)
    <a href="{{ $disabled ? '#' : $href }}" {{ $attributes->merge(['class' => $classes]) }}
        @if ($onclick && !$disabled) onclick="{{ $onclick }}" @endif
        @if ($disabled) aria-disabled="true" tabindex="-1" @endif>
        @if ($icon)
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4 mr-2" />
        @endif
        {{ $text }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}
        @if ($onclick && !$disabled) onclick="{{ $onclick }}" @endif
        @if ($disabled) disabled @endif>
        @if ($icon)
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4 mr-2" />
        @endif
        {{ $text }}
    </button>
@endif
