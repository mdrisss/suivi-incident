<?php

namespace App\Models;

use App\Enums\Categorie;
use App\Enums\Severite;
use App\Enums\StatutIncident;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'titre', 'description', 'severite', 'statut', 'categorie',
    'site_id', 'equipement_id', 'liaison_id', 'declare_par_id', 'assigne_a_id',
    'debut_at', 'resolu_at', 'impact', 'cause_racine', 'solution',
])]
class Incident extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'severite' => Severite::class,
            'statut' => StatutIncident::class,
            'categorie' => Categorie::class,
            'debut_at' => 'datetime',
            'resolu_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // La référence dépend de l'id, on la calcule donc juste après l'insertion.
        static::created(function (Incident $incident) {
            $incident->reference = sprintf('INC-%s-%05d', $incident->debut_at->format('Y'), $incident->id);
            $incident->saveQuietly();
        });
    }

    /**
     * @return BelongsTo<Site, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * @return BelongsTo<Equipement, $this>
     */
    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class);
    }

    /**
     * @return BelongsTo<Liaison, $this>
     */
    public function liaison(): BelongsTo
    {
        return $this->belongsTo(Liaison::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function declarePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'declare_par_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assigneA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigne_a_id');
    }

    /**
     * @return HasMany<Suivi, $this>
     */
    public function suivis(): HasMany
    {
        return $this->hasMany(Suivi::class)->latest();
    }

    /**
     * @param  Builder<Incident>  $query
     */
    public function scopeActifs(Builder $query): void
    {
        $query->whereIn('statut', StatutIncident::actifs());
    }

    /**
     * @param  Builder<Incident>  $query
     * @param  array<string, mixed>  $filtres
     */
    public function scopeFiltrer(Builder $query, array $filtres): void
    {
        $query
            ->when($filtres['q'] ?? null, fn (Builder $q, string $texte) => $q->where(
                fn (Builder $q) => $q->where('reference', 'like', "%{$texte}%")->orWhere('titre', 'like', "%{$texte}%")
            ))
            ->when($filtres['statut'] ?? null, fn (Builder $q, string $statut) => $q->where('statut', $statut))
            ->when($filtres['severite'] ?? null, fn (Builder $q, string $severite) => $q->where('severite', $severite))
            ->when($filtres['site_id'] ?? null, fn (Builder $q, string $siteId) => $q->where('site_id', $siteId))
            ->when($filtres['du'] ?? null, fn (Builder $q, string $du) => $q->whereDate('debut_at', '>=', $du))
            ->when($filtres['au'] ?? null, fn (Builder $q, string $au) => $q->whereDate('debut_at', '<=', $au));
    }

    /**
     * Durée de l'incident en minutes (jusqu'à maintenant s'il n'est pas résolu).
     */
    public function dureeMinutes(): int
    {
        return (int) $this->debut_at->diffInMinutes($this->resolu_at ?? now());
    }

    public function dureeLisible(): string
    {
        return self::formaterDuree($this->dureeMinutes());
    }

    public static function formaterDuree(int|float|null $minutes): string
    {
        if ($minutes === null) {
            return '-';
        }

        $minutes = (int) round($minutes);
        $jours = intdiv($minutes, 1440);
        $heures = intdiv($minutes % 1440, 60);
        $reste = $minutes % 60;

        return trim(($jours ? "{$jours}j " : '').($heures ? "{$heures}h " : '')."{$reste}min");
    }
}
