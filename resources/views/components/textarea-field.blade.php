{{-- resources/views/components/textarea-field.blade.php --}}
<div x-data="{
    touched: {{ $touched ? 'true' : 'false' }},
    charCount: 0,
    maxLength: {{ $maxLength }},
    errorMessage: @js($errorMessage),
    required: {{ $required ? 'true' : 'false' }},
    updateCount($el) {
        this.charCount = $el.value.length
    }
}">
    <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <textarea @if ($id) id="{{ $id }}" @endif
            @if ($name) name="{{ $name }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            @if ($maxLength > 0) maxlength="{{ $maxLength }}" @endif rows="{{ $rows }}"
            @blur="touched = true" @input="touched = true; updateCount($el)" x-init="updateCount($el)"
            {{ $attributes->merge(['class' => $getTextareaClasses()]) }}>{{ $value }}</textarea>

        @if ($showCharacterCount && $maxLength > 0)
            <div class="absolute bottom-3 right-3 text-sm"
                :class="{ 'text-red-500': charCount >= maxLength, 'text-gray-500': charCount < maxLength }">
                <span x-text="charCount"></span>/<span x-text="maxLength"></span>
            </div>
        @endif
    </div>

    @if ($errorMessage)
        <p x-show="touched" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            class="mt-2 text-sm text-red-600 dark:text-red-500">
            <span class="font-medium">Error:</span> {{ $errorMessage }}
        </p>
    @endif
</div>
