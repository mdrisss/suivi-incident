<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'site_a_id', 'site_b_id', 'type', 'capacite', 'operateur', 'statut'])]
class Liaison extends Model
{
    public const TYPES = ['Fibre optique', 'Faisceau hertzien', 'Satellite', 'Câble sous-marin', 'MPLS opérateur'];

    public const STATUTS = ['actif' => 'Active', 'degrade' => 'Dégradée', 'coupe' => 'Coupée'];

    /**
     * @return BelongsTo<Site, $this>
     */
    public function siteA(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_a_id');
    }

    /**
     * @return BelongsTo<Site, $this>
     */
    public function siteB(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_b_id');
    }

    /**
     * @return HasMany<Incident, $this>
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function statutLabel(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }
}
