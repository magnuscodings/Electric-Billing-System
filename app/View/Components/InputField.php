<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputField extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $id = '',
        public string $name = '',
        public string $label = '',
        public string $type = 'text',
        public string|null $value = '',
        public string|null $errorMessage = null,
        public string|null $placeholder = null,
        public bool $required = false,
        public bool $touched = false  // New property to track field interaction
    ) {}

    public function render()
    {
        return view('components.input-field');
    }

    public function shouldShowError(): bool
    {
        return $this->touched && !is_null($this->errorMessage);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getInputClasses(): string
    {
        if ($this->getType() === 'email') {
            $baseClasses = 'text-sm rounded-lg block w-full p-2.5 ';
        } else {
            $baseClasses = 'text-sm rounded-lg block w-full p-2.5 capitalize ';
        }

        if ($this->shouldShowError()) {
            return $baseClasses . 'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500';
        }

        return $baseClasses . 'bg-gray-50 border border-gray-300 text-gray-900 focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500';
    }
}
