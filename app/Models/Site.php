<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'nom', 'ville', 'type', 'adresse'])]
class Site extends Model
{
    use HasFactory;

    public const TYPES = ['POP', 'Data center', 'Station FH', 'Point d\'atterrissement', 'Shelter'];

    /**
     * @return HasMany<Equipement, $this>
     */
    public function equipements(): HasMany
    {
        return $this->hasMany(Equipement::class);
    }

    /**
     * @return HasMany<Incident, $this>
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function libelle(): string
    {
        return "{$this->code} - {$this->nom}";
    }
}
