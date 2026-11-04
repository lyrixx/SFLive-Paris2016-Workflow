<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login/alice', server: ['HTTP_REFERER' => '/tasks']);

        self::assertResponseRedirects('/tasks');

        $client->followRedirect();

        self::assertSelectorTextContains('nav', 'Alice');
    }
}
