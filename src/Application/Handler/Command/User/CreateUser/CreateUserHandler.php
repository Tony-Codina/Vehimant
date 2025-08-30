<?php

namespace App\Application\Handler\Command\User\CreateUser;

use App\Application\Command\User\CreateUser\CreateUserCommand;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(CreateUserCommand $command): string
    {
        $email = strtolower(trim($command->email));

        if ($this->userRepository->emailExists($email)) {
            throw new \DomainException('Email already exists');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles($command->roles);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $command->plainPassword);
        $user->setPassword($hashedPassword);

        // Asigna un id manualmente si no existe (tests/unitarios)
        if ($user->getId() === null) {
            $reflection = new \ReflectionClass($user);
            $idProp = $reflection->getProperty('id');
            $idProp->setAccessible(true);
            $idProp->setValue($user, \Symfony\Component\Uid\Uuid::v4());
        }
        $this->userRepository->add($user);

        return $user->getIdAsString();
    }
}
