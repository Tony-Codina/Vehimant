<?php

namespace Tests\Functional\UI\Controller;

use App\Application\Command\User\CreateUser\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CreateUserControllerTest extends WebTestCase
{
    public function test_register_enqueues_command_and_returns_202(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/new-user',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'email' => 'func@test.com',
                'plainPassword' => 'Password#1234',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(202);

        // Transport in-memory configurado en config/packages/test/messenger.yaml
        $transport = static::getContainer()->get('messenger.transport.async');
        $envelopes = $transport->get();

        self::assertCount(1, $envelopes);
        self::assertInstanceOf(CreateUserCommand::class, $envelopes[0]->getMessage());

        /** @var CreateUserCommand $msg */
        $msg = $envelopes[0]->getMessage();
        self::assertSame('func@test.com', $msg->email);
        self::assertSame(['ROLE_USER'], $msg->roles);
    }
}
