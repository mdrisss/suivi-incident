<?php

namespace App\Enums;

enum Categorie: string
{
    case CoupureFibre = 'coupure_fibre';
    case PanneMaterielle = 'panne_materielle';
    case Energie = 'energie';
    case Configuration = 'configuration';
    case Saturation = 'saturation';
    case Securite = 'securite';
    case Autre = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::CoupureFibre => 'Coupure fibre',
            self::PanneMaterielle => 'Panne matérielle',
            self::Energie => 'Énergie / climatisation',
            self::Configuration => 'Erreur de configuration',
            self::Saturation => 'Saturation / congestion',
            self::Securite => 'Sécurité (DDoS, intrusion)',
            self::Autre => 'Autre',
        };
    }
}
