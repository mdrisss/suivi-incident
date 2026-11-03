<?php

namespace App\Enums;

enum TypeSuivi: string
{
    case Commentaire = 'commentaire';
    case Intervention = 'intervention';
    case ChangementStatut = 'changement_statut';

    public function label(): string
    {
        return match ($this) {
            self::Commentaire => 'Commentaire',
            self::Intervention => 'Intervention terrain',
            self::ChangementStatut => 'Changement de statut',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Commentaire => 'chat-left-text',
            self::Intervention => 'tools',
            self::ChangementStatut => 'arrow-repeat',
        };
    }
}
