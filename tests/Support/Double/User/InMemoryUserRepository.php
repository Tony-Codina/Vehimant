<?php

    namespace App\Tests\Support\Double\User;

    use App\Domain\User\Entity\User;
    use App\Domain\User\Repository\UserRepositoryInterface;

    final class InMemoryUserRepository implements UserRepositoryInterface
    {
        /** @var array<string, User> */
        private array $items = [];

        public function add(User $user): void
        {
            $this->items[trim(strtolower($user->getEmail()))] = $user;
        }

        public function emailExists(string $email): bool
        {
            return isset($this->items[trim(strtolower($email))]);
        }

        public function findByEmail(string $email): ?User
        {
            $email = trim(strtolower($email));
            return $this->items[$email] ?? null;
        }

        public function findByIdUser(string $id): ?User
        {
            foreach ($this->items as $user) {
                if ((string)$user->getId() === (string)$id) {
                    return $user;
                }
            }
            return null;
        }

        public function remove(User $user): void
        {
            unset($this->items[trim(strtolower($user->getEmail()))]);
        }
    }
