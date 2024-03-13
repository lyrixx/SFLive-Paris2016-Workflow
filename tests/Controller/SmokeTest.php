<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public static function providePages(): iterable
    {
        yield ['/', 'symfony/workflow', '.container-fluid h1'];
        yield ['/tasks'];
        yield ['/articles'];
    }

    /** @dataProvider providePages */
    public function test(string $page, ?string $expected = null, ?string $selector = null): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $page);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        if ($expected && $selector) {
            $this->assertStringContainsString($expected, $crawler->filter($selector)->text());
        }
    }
}
