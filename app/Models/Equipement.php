<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['site_id', 'hostname', 'type', 'constructeur', 'modele', 'ip_management', 'statut'])]
class Equipement extends Model
{
    use HasFactory;

    public const TYPES = ['Routeur', 'Switch', 'DWDM', 'Firewall', 'OLT', 'Faisceau hertzien', 'Serveur'];

    public const CONSTRUCTEURS = ['Cisco', 'Huawei', 'Juniper', 'Nokia', 'Ericsson', 'Fortinet', 'Autre'];

    public const STATUTS = ['actif' => 'Actif', 'maintenance' => 'En maintenance', 'hors_service' => 'Hors service'];

    /**
     * @return BelongsTo<Site, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
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
