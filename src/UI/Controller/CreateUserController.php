<?php

namespace App\UI\Controller;

use App\Application\Command\User\CreateUser\CreateUserCommand;
use App\UI\Http\RequestDto\User\CreateUserRequestDto;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserController
{
    #[Route('/api/new-user', name: 'api_new_user', methods: ['POST'])]
    public function register(
        Request $request,
        MessageBusInterface $commandBus,
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
            $envelope = $commandBus->dispatch($dto->toCommand());
            $handled  = $envelope->last(HandledStamp::class);

            if ($handled) {
                $id = $handled->getResult();
                return new JsonResponse([
                    'id' => $id,
                    'email' => strtolower(trim($dto->email)),
                    'roles' => ['ROLE_USER']
                ], 201);
            }

            $msgIdStamp = $envelope->last(TransportMessageIdStamp::class);
            return new JsonResponse([
                'status' => 'queued',
                'messageId' => $msgIdStamp?->getId()
            ], 202);
        } catch (HandlerFailedException $e) {
            $prev = $e->getPrevious();
            if ($prev instanceof \DomainException) {
                return new JsonResponse(['error' => $prev->getMessage()], 409);
            }
            throw $e;
        }
    }
}
