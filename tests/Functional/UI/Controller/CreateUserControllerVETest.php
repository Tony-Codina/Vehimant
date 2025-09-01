<?php

namespace Tests\Functional\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CreateUserControllerVETest extends WebTestCase
{
    public function test_register_validation_errors_return_422(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/new-user',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'email' => 'not-an-email',
                'plainPassword' => 'short',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(422);

        $json = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('errors', $json);
        self::assertNotEmpty($json['errors']);
    }
}
