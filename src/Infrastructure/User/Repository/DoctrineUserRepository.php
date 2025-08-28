<?php

namespace App\Infrastructure\User\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function add(User $user): void
    {
        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new \DomainException(sprintf('User with email "%s" already exists.', $user->getEmail()));
        }
    }

    public function emailExists(string $email): bool
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where('LOWER(u.email) = :email')
            ->setParameter('email', strtolower(trim($email)));

        return (int)$qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findByEmail(string $email): ?User
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where('LOWER(u.email) = :email')
            ->setParameter('email', strtolower(trim($email)))
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByIdUser(string $id): ?User
    {
        return $this->em->getRepository(User::class)->find($id);
    }
}
