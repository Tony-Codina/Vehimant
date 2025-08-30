<?php

namespace Tests\Unit\Application\Handler\Command\User\CreateUser;

use App\Application\Command\User\CreateUser\CreateUserCommand;
use App\Application\Handler\Command\User\CreateUser\CreateUserHandler;
use App\Domain\User\Entity\User;
use App\Tests\Support\Double\User\InMemoryUserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserHandlerTest extends TestCase
{
    public function test_creates_user_successfully(): void
    {
        $repo   = new InMemoryUserRepository();
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $hasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), 'password123')
            ->willReturn('hashed-password');

        $handler = new CreateUserHandler($repo, $hasher);

        // Email con espacios y mayúsculas para comprobar normalización
        $command = new CreateUserCommand('  TEST@example.com  ', 'password123', ['ROLE_USER']);
        $id = $handler($command);

        $user = $repo->findByEmail('test@example.com');
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertNotEmpty($user->getPassword());
    $this->assertIsString($id);
    $this->assertNotEmpty($id);
    }

    public function test_throws_exception_if_email_exists(): void
    {
        $repo   = new InMemoryUserRepository();
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

    $user = new User();
    $user->setEmail('duplicate@example.com');
    $user->setPassword('hashed');
    $user->setRoles(['ROLE_USER']);
    $repo->add($user);

        $handler = new CreateUserHandler($repo, $hasher);

        $this->expectException(\DomainException::class);
        $handler(new CreateUserCommand('duplicate@example.com', 'password123', ['ROLE_USER']));
    }

    public function test_bubbles_domain_exception_from_repository_add(): void
    {
        $repo   = new InMemoryUserRepository();
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $hasher->method('hashPassword')->willReturn('hashed');

        // Simula violación de unicidad añadiendo el usuario antes
    $user = new User();
    $user->setEmail('x@example.com');
    $user->setPassword('hashed');
    $user->setRoles(['ROLE_USER']);
    $repo->add($user);

        $handler = new CreateUserHandler($repo, $hasher);

        $this->expectException(\DomainException::class);
        $handler(new CreateUserCommand('x@example.com', 'password123', ['ROLE_USER']));
    }
}