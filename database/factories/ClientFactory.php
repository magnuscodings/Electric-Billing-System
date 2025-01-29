<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->firstName(), // Assuming middle name is optional
            'suffix' => $this->faker->randomElement(['Jr.', 'Sr.', 'III', 'IV']), // Adjust based on common suffixes
            'address' => $this->faker->address(),
            'meter_code' => $this->faker->unique()->randomNumber(8), // Adjust based on your meter code format
            'stall_number' => $this->faker->unique()->randomNumber(8), // Adjust based on your meter code format
        ];
    }
}
