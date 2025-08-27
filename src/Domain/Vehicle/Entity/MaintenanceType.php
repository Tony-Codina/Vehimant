<?php
namespace App\Domain\Vehicle\Entity;

enum MaintenanceType: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';

    public function intervalKm(): int
    {
        return match ($this) {
            self::A => 10_000,
            self::B => 20_000,
            self::C => 40_000,
        };
    }
}
