<div x-data="{
    touched: {{ $touched ? 'true' : 'false' }},
    errorMessage: @js($errorMessage),
    required: {{ $required ? 'true' : 'false' }}
}">
    <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white capitalize">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <input @if ($id) id="{{ $id }}" @endif
        @if ($name) name="{{ $name }}" @endif type="{{ $type }}"
        value="{{ $value }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($required) required @endif @blur="touched = true" @input="touched = true"
        {{ $attributes->merge(['class' => $getInputClasses()]) }}>

    @if ($errorMessage)
        <p x-show="touched" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            class="mt-2 text-sm text-red-600 dark:text-red-500">
            <span class="font-medium">Error:</span> {{ $errorMessage }}
        </p>
    @endif
</div>
