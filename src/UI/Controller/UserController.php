<?php

namespace App\UI\Controller;

use App\Application\Command\User\CreateUser\CreateUserCommand;
use App\Application\Handler\Command\User\CreateUser\CreateUserHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\UI\Http\RequestDto\User\CreateUserRequestDto;

class UserController
{
    #[Route('/api/users', name: 'api_create_user', methods: ['POST'])]
    public function register(
        Request $request,
        CreateUserHandler $handler,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        $dto = CreateUserRequestDto::fromArray($data);
        $violations = $validator->validate($dto);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 422);
        }

        try {
            $command = $dto->toCommand();
            $id = $handler($command);
            return new JsonResponse([
                'id' => $id,
                'email' => strtolower(trim($dto->email)),
                'roles' => ['ROLE_USER']
            ], 201);
        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        }
    }
}
