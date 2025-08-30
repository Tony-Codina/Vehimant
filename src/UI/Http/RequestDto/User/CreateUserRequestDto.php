<?php

namespace App\UI\Http\RequestDto\User;

use App\Application\Command\User\CreateUser\CreateUserCommand;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserRequestDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $plainPassword;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->email = (string)($data['email'] ?? '');
        $dto->plainPassword = (string)($data['plainPassword'] ?? '');
        return $dto;
    }

    public function toCommand(): CreateUserCommand
    {
        return new CreateUserCommand($this->email, $this->plainPassword, ['ROLE_USER']);
    }
}
