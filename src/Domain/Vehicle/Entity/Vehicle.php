<?php

namespace App\Domain\Vehicle\Entity;

use App\Domain\Vehicle\Entity\MaintenanceType;
use App\Domain\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity]
#[ORM\Table(name: 'vehicle')]
class Vehicle
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    private Ulid $id;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column(length: 60, unique: true)]
    private string $plate;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'integer')]
    private int $odometerKm;

    #[ORM\Column(enumType: MaintenanceType::class)]
    private MaintenanceType $lastMaintenanceType;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $lastMaintenanceAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    public function __construct(
        string $name,
        string $plate,
        int $odometerKm,
        MaintenanceType $lastMaintenanceType,
        \DateTimeImmutable $lastMaintenanceAt,
        User $owner
    ) {
        $this->id = new Ulid();
        $this->name = $name;
        $this->plate = $plate;
        $this->createdAt = new \DateTimeImmutable('now');
        $this->odometerKm = $odometerKm;
        $this->lastMaintenanceType = $lastMaintenanceType;
        $this->lastMaintenanceAt = $lastMaintenanceAt;
        $this->owner = $owner;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getOwnerId(): string
    {
        return $this->owner->getIdAsString();
    }

    public function id(): Ulid { return $this->id; }
    public function name(): string { return $this->name; }
    public function plate(): string { return $this->plate; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    public function odometerKm(): int { return $this->odometerKm; }
    public function lastMaintenanceType(): MaintenanceType { return $this->lastMaintenanceType; }
    public function lastMaintenanceAt(): \DateTimeImmutable { return $this->lastMaintenanceAt; }

    public function nextMaintenanceDueKm(): int
    {
        return $this->odometerKm + $this->lastMaintenanceType->intervalKm();
    }
    // ...existing code...
}
