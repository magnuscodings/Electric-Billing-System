<?php

namespace App\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class SelectField extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $id = '',
        public string $name = '',
        public string $label = '',
        public array|Collection $options = [],
        public string|int|null $value = null,
        public string|null $errorMessage = null,
        public string|null $placeholder = null,
        public bool $required = false,
        public bool $searchable = false,
        public bool $multiple = false,
        public bool $touched = false  // New property to track field interaction
    ) {
        if ($options instanceof Collection) {
            $this->options = $options->toArray();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.select-field');
    }

    /**
     * Determine if the select field has an error and should show it.
     */
    public function shouldShowError(): bool
    {
        return $this->touched && !is_null($this->errorMessage);
    }

    /**
     * Get the select classes based on error state.
     */
    public function getSelectClasses(): string
    {
        $baseClasses = 'text-sm rounded-lg block w-full p-2.5 ';

        if ($this->shouldShowError()) {
            return $baseClasses . 'bg-red-50 border border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-red-500 dark:border-red-500';
        }

        return $baseClasses . 'bg-gray-50 border border-gray-300 text-gray-900 focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500';
    }

    /**
     * Check if options contain any optgroups
     */
    public function hasOptgroups(): bool
    {
        return collect($this->options)->contains(function ($value, $key) {
            return is_array($value) && !is_numeric($key);
        });
    }

    /**
     * Check if a value is selected
     */
    public function isSelected($optionValue): bool
    {
        if ($this->multiple && is_array($this->value)) {
            return in_array($optionValue, $this->value);
        }
        return $optionValue == $this->value;
    }
}
