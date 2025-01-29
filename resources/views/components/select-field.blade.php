{{-- resources/views/components/select-field.blade.php --}}
<div x-data="{
    touched: {{ $touched ? 'true' : 'false' }},
    errorMessage: @js($errorMessage),
    required: {{ $required ? 'true' : 'false' }}
}">
    <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        @if ($searchable)
            <select @if ($id) id="{{ $id }}" @endif
                @if ($name) name="{{ $name }}{{ $multiple ? '[]' : '' }}" @endif
                @if ($required) required @endif @if ($multiple) multiple @endif
                @blur="touched = true" @change="touched = true"
                {{ $attributes->merge(['class' => $getSelectClasses() . ' select2']) }}>
            @else
                <select @if ($id) id="{{ $id }}" @endif
                    @if ($name) name="{{ $name }}{{ $multiple ? '[]' : '' }}" @endif
                    @if ($required) required @endif @if ($multiple) multiple @endif
                    @blur="touched = true" @change="touched = true"
                    {{ $attributes->merge(['class' => $getSelectClasses()]) }}>
        @endif

        @if ($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if ($hasOptgroups())
            @foreach ($options as $groupLabel => $groupOptions)
                <optgroup label="{{ $groupLabel }}">
                    @foreach ($groupOptions as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}" @if (isSelected($optionValue)) selected @endif>
                            {{ $optionLabel }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        @else
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @if (isSelected($optionValue)) selected @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        @endif
        </select>

        @if (!$searchable)
            <div
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                </svg>
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

    @if ($searchable)
        @push('styles')
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        @endpush

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    $('#{{ $id }}').select2({
                        theme: 'classic',
                        placeholder: @json($placeholder),
                        allowClear: true,
                        width: '100%'
                    }).on('select2:open', function() {
                        Alpine.evaluate(document.getElementById('{{ $id }}')._x_dataStack[0],
                            'touched = true');
                    });
                });
            </script>
        @endpush
    @endif
</div>
