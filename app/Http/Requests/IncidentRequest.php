<?php

namespace App\Http\Requests;

use App\Enums\Categorie;
use App\Enums\Severite;
use App\Enums\StatutIncident;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $termine = 'required_if:statut,'.StatutIncident::Resolu->value.','.StatutIncident::Clos->value;

        return [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'severite' => ['required', Rule::enum(Severite::class)],
            'statut' => ['required', Rule::enum(StatutIncident::class)],
            'categorie' => ['required', Rule::enum(Categorie::class)],
            'site_id' => ['required', 'exists:sites,id'],
            'equipement_id' => ['nullable', 'exists:equipements,id'],
            'liaison_id' => ['nullable', 'exists:liaisons,id'],
            'assigne_a_id' => ['nullable', 'exists:users,id'],
            'debut_at' => ['required', 'date'],
            'resolu_at' => ['nullable', 'date', 'after_or_equal:debut_at'],
            'impact' => ['nullable', 'string'],
            'cause_racine' => ['nullable', 'string', $termine],
            'solution' => ['nullable', 'string', $termine],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'titre' => 'titre',
            'severite' => 'sévérité',
            'categorie' => 'catégorie',
            'site_id' => 'site',
            'equipement_id' => 'équipement',
            'liaison_id' => 'liaison',
            'assigne_a_id' => 'technicien assigné',
            'debut_at' => 'date de début',
            'resolu_at' => 'date de résolution',
            'cause_racine' => 'cause racine',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cause_racine.required_if' => 'La cause racine est obligatoire pour un incident résolu ou clos.',
            'solution.required_if' => 'La solution est obligatoire pour un incident résolu ou clos.',
        ];
    }
}
