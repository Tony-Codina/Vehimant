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

    #[ORM\Column(type: 'string', length: 17, unique: true)]
    private string $vin;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $odometerKm;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $itv = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $nextItv = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $nextMaintenanceAt = null;

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
        string $vin,
        int $odometerKm,
        MaintenanceType $lastMaintenanceType,
        \DateTimeImmutable $lastMaintenanceAt,
        User $owner,
        ?\DateTimeImmutable $itv = null,
        ?\DateTimeImmutable $nextItv = null,
        ?\DateTimeImmutable $nextMaintenanceAt = null
    ) {
        $this->id = new Ulid();
        $this->name = $name;
        $this->plate = strtoupper($plate);
        $vin = strtoupper($vin);
        if (strlen($vin) !== 17) {
            throw new \InvalidArgumentException('VIN debe tener 17 caracteres');
        }
        $this->vin = $vin;
        $this->createdAt = new \DateTimeImmutable('now');
        if ($odometerKm < 0) {
            throw new \InvalidArgumentException('El odómetro no puede ser negativo');
        }
        $this->odometerKm = $odometerKm;
        $this->lastMaintenanceType = $lastMaintenanceType;
        $this->lastMaintenanceAt = $lastMaintenanceAt;
        $this->owner = $owner;
        if ($itv && $nextItv && $nextItv < $itv) {
            throw new \InvalidArgumentException('La próxima ITV debe ser igual o posterior a la última ITV');
        }
        $this->itv = $itv;
        $this->nextItv = $nextItv;
        $this->nextMaintenanceAt = $nextMaintenanceAt;
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

    public function getId(): Ulid { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPlate(): string { return $this->plate; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getVin(): string
    {
        return $this->vin;
    }

    public function getItv(): ?\DateTimeImmutable
    {
        return $this->itv;
    }

    public function setItv(?\DateTimeImmutable $itv): self
    {
        if ($this->nextItv && $itv && $this->nextItv < $itv) {
            throw new \InvalidArgumentException('La próxima ITV debe ser igual o posterior a la última ITV');
        }
        $this->itv = $itv;
        return $this;
    }

    public function getNextItv(): ?\DateTimeImmutable
    {
        return $this->nextItv;
    }

    public function setNextItv(?\DateTimeImmutable $nextItv): self
    {
        if ($this->itv && $nextItv && $nextItv < $this->itv) {
            throw new \InvalidArgumentException('La próxima ITV debe ser igual o posterior a la última ITV');
        }
        $this->nextItv = $nextItv;
        return $this;
    }

    public function getNextMaintenanceAt(): ?\DateTimeImmutable
    {
        return $this->nextMaintenanceAt;
    }

    public function setNextMaintenanceAt(?\DateTimeImmutable $nextMaintenanceAt): self
    {
        $this->nextMaintenanceAt = $nextMaintenanceAt;
        return $this;
    }

    public function getOdometerKm(): int { return $this->odometerKm; }

    public function setOdometerKm(int $odometerKm): self
    {
        if ($odometerKm < 0) {
            throw new \InvalidArgumentException('El odómetro no puede ser negativo');
        }
        if ($odometerKm < $this->odometerKm) {
            throw new \InvalidArgumentException('El odómetro no puede disminuir');
        }
        $this->odometerKm = $odometerKm;
        return $this;
    }

        public function getLastMaintenanceType(): MaintenanceType { return $this->lastMaintenanceType; }

    public function setLastMaintenanceType(MaintenanceType $type): self
    {
        $this->lastMaintenanceType = $type;
        return $this;
    }

        public function getLastMaintenanceAt(): \DateTimeImmutable { return $this->lastMaintenanceAt; }

    public function getNextMaintenanceDueKm(): int
    {
        return $this->odometerKm + $this->lastMaintenanceType->intervalKm();
    }
}