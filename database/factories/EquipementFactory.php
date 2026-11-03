<?php

namespace Database\Factories;

use App\Models\Equipement;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipement>
 */
class EquipementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'site_id' => Site::factory(),
            'hostname' => strtolower(fake()->unique()->bothify('rtr-???-##')),
            'type' => fake()->randomElement(Equipement::TYPES),
            'constructeur' => fake()->randomElement(Equipement::CONSTRUCTEURS),
            'modele' => strtoupper(fake()->bothify('??-####')),
            'ip_management' => fake()->localIpv4(),
            'statut' => 'actif',
        ];
    }
}
