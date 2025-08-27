<?php

namespace App\Infrastructure\Vehicle\Repository;

use App\Domain\Vehicle\Entity\Vehicle;
use App\Domain\Vehicle\Repository\VehicleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Ulid;

final class DoctrineVehicleRepository extends ServiceEntityRepository implements VehicleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function findById(Ulid $id): ?Vehicle
    {
        return parent::find($id);
    }

    public function save(Vehicle $vehicle): void
    {
        $em = $this->getEntityManager();
        $em->persist($vehicle);
        $em->flush();
    }
    public function listByOwner(Ulid $ownerId, int $page = 1, int $perPage = 25): array
    {
        $qb = $this->createQueryBuilder('v')
            ->where('v.ownerId = :ownerId')
            ->setParameter('ownerId', (string) $ownerId)
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);
        return $qb->getQuery()->getResult();
    }

    public function remove(Vehicle $vehicle): void
    {
        $em = $this->getEntityManager();
        $em->remove($vehicle);
        $em->flush();
    }
}
