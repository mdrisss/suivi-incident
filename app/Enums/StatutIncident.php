<?php

namespace App\Enums;

enum StatutIncident: string
{
    case Ouvert = 'ouvert';
    case EnCours = 'en_cours';
    case Resolu = 'resolu';
    case Clos = 'clos';

    public function label(): string
    {
        return match ($this) {
            self::Ouvert => 'Ouvert',
            self::EnCours => 'En cours',
            self::Resolu => 'Résolu',
            self::Clos => 'Clos',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ouvert => 'danger',
            self::EnCours => 'primary',
            self::Resolu => 'success',
            self::Clos => 'secondary',
        };
    }

    public function estTermine(): bool
    {
        return in_array($this, [self::Resolu, self::Clos], true);
    }

    /**
     * @return list<self>
     */
    public static function actifs(): array
    {
        return [self::Ouvert, self::EnCours];
    }
}
