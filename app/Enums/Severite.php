<?php

namespace App\Enums;

enum Severite: string
{
    case Critique = 'critique';
    case Majeure = 'majeure';
    case Mineure = 'mineure';

    public function label(): string
    {
        return match ($this) {
            self::Critique => 'P1 - Critique',
            self::Majeure => 'P2 - Majeure',
            self::Mineure => 'P3 - Mineure',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Critique => 'danger',
            self::Majeure => 'warning',
            self::Mineure => 'info',
        };
    }
}
