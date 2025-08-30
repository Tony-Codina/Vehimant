<?php

namespace Tests\Unit\UI\Http\RequestDto\User;

use App\UI\Http\RequestDto\User\CreateUserRequestDto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class CreateUserRequestDtoTest extends TestCase
{
    public function test_validation_fails_with_bad_email_and_short_password(): void
    {
        $dto = CreateUserRequestDto::fromArray([
            'email' => 'bad',
            'plainPassword' => 'short',
        ]);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($dto);

        $this->assertGreaterThan(0, \count($violations));
    }

    public function test_to_command_maps_fields(): void
    {
        $dto = CreateUserRequestDto::fromArray([
            'email' => 'User@Demo.com',
            'plainPassword' => 'Password#1234',
        ]);

        $cmd = $dto->toCommand();

        $this->assertSame('User@Demo.com', $cmd->email);
        $this->assertSame('Password#1234', $cmd->plainPassword);
        $this->assertSame(['ROLE_USER'], $cmd->roles);
    }
}
