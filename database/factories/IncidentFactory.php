<?php

namespace Database\Factories;

use App\Enums\Categorie;
use App\Enums\Severite;
use App\Enums\StatutIncident;
use App\Models\Incident;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'severite' => fake()->randomElement(Severite::cases()),
            'statut' => StatutIncident::Ouvert,
            'categorie' => fake()->randomElement(Categorie::cases()),
            'site_id' => Site::factory(),
            'declare_par_id' => User::factory(),
            'debut_at' => fake()->dateTimeBetween('-6 weeks'),
            'impact' => fake()->sentence(),
        ];
    }

    public function resolu(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => StatutIncident::Resolu,
            'resolu_at' => (clone $attributes['debut_at'])->modify('+'.fake()->numberBetween(15, 2880).' minutes'),
            'cause_racine' => fake()->sentence(),
            'solution' => fake()->sentence(),
        ]);
    }
}
