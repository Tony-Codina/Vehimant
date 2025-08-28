<?php

namespace App\Application\Command\User\CreateUser;

class CreateUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $plainPassword,
        public readonly array $roles = ['ROLE_USER']
    ) {}
}
