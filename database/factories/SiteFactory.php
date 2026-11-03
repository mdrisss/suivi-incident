<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('POP-???##')),
            'nom' => 'POP '.fake()->city(),
            'ville' => fake()->city(),
            'type' => fake()->randomElement(Site::TYPES),
            'adresse' => fake()->streetAddress(),
        ];
    }
}
