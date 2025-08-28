<?php

namespace Tests\Unit\Application\Handler\Command\User\CreateUser;

use App\Application\Command\User\CreateUser\CreateUserCommand;
use App\Application\Handler\Command\User\CreateUser\CreateUserHandler;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserHandlerTest extends TestCase
{
    public function test_creates_user_successfully(): void
    {
        $repo   = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $repo->method('emailExists')->willReturn(false);

        // Verificamos que add() reciba un User con email normalizado y roles asignados
        $repo->expects($this->once())
            ->method('add')
            ->with($this->callback(function (User $u) {
                $this->assertSame('test@example.com', $u->getEmail());
                $this->assertContains('ROLE_USER', $u->getRoles());
                $this->assertNotEmpty($u->getPassword());
                return true;
            }));

        // Verificamos que hashPassword se llama con el mismo User y el plainPassword
        $hasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), 'password123')
            ->willReturn('hashed-password');

        $handler = new CreateUserHandler($repo, $hasher);

        // Email con espacios y mayúsculas para comprobar normalización
        $command = new CreateUserCommand('  TEST@example.com  ', 'password123', ['ROLE_USER']);
        $id = $handler($command);

    $this->assertIsString($id);
    }

    public function test_throws_exception_if_email_exists(): void
    {
        $repo   = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $repo->method('emailExists')->willReturn(true);

        $handler = new CreateUserHandler($repo, $hasher);

        $this->expectException(\DomainException::class);
        $handler(new CreateUserCommand('duplicate@example.com', 'password123', ['ROLE_USER']));
    }

    public function test_bubbles_domain_exception_from_repository_add(): void
    {
        $repo   = $this->createMock(UserRepositoryInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $repo->method('emailExists')->willReturn(false);
        $hasher->method('hashPassword')->willReturn('hashed');

        // Simula violación de unicidad traducida a DomainException por el repo
        $repo->method('add')->willThrowException(new \DomainException('User with email "x" already exists.'));

        $handler = new CreateUserHandler($repo, $hasher);

        $this->expectException(\DomainException::class);
        $handler(new CreateUserCommand('x@example.com', 'password123', ['ROLE_USER']));
    }
}
