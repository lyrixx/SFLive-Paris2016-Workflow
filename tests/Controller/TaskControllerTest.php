<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testApplyTransition(): void
    {
        $client = static::createClient();

        $client->request('POST', '/tasks/create', ['title' => 'Test']);
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Test');
        self::assertSelectorTextContains('.wf-band .wf-place.is-marked', 'new');

        $client->submitForm('start_process');
        $client->followRedirect();

        self::assertSelectorTextContains('.wf-band .wf-place.is-marked', 'processing');
        self::assertSelectorTextContains('main', 'TaskStep::Processing');
        self::assertSelectorExists('[data-controller="mermaid"][data-mermaid-definition-value*="stroke-width:4px"]');
    }
}
