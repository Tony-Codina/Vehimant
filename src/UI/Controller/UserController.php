<?php

namespace App\UI\Controller;

use App\Application\Command\User\CreateUser\CreateUserCommand;
use App\Application\Handler\Command\User\CreateUser\CreateUserHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserController
{
    #[Route('/api/users', name: 'api_create_user', methods: ['POST'])]
    public function register(
        Request $request,
        CreateUserHandler $handler,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];

        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'plainPassword' => [new Assert\NotBlank(), new Assert\Length(['min' => 8])]
        ]);

        $violations = $validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 422);
        }

        try {
            $command = new CreateUserCommand(
                $data['email'],
                $data['plainPassword'],
                ['ROLE_USER']
            );
            $id = $handler($command);

            return new JsonResponse([
                'id' => $id,
                'email' => strtolower(trim($data['email'])),
                'roles' => ['ROLE_USER']
            ], 201);
        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        }
    }
}
