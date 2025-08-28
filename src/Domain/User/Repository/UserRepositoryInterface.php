<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;

interface UserRepositoryInterface
{
    public function add(User $user): void;

    public function emailExists(string $email): bool;

    public function findByEmail(string $email): ?User;

    public function findByIdUser(string $id): ?User;
}
