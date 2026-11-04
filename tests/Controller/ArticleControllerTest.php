<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    public function testGuardBlocksTheTransition(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login/journalist');

        $client->request('POST', '/articles/create', ['title' => 'Test']);
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.wf-band .wf-place.is-marked', 'draft');

        $client->submitForm('request review');
        $client->followRedirect();

        self::assertSelectorTextContains('.wf-band', 'waiting for journalist');
        self::assertSelectorTextContains('.wf-band', 'wait for spellchecker');
        self::assertSelectorTextContains('.wf-timeline', 'journalist');
        self::assertSelectorNotExists('button[value="journalist approval"][disabled]');
        self::assertSelectorExists('button[value="spellchecker approval"][disabled]');
        self::assertSelectorTextContains('.wf-blockers', "is_granted('ROLE_SPELLCHECKER')");
    }
}
