<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    #[DataProvider('provideCases')]
    public function test(string $page, ?string $expected = null, ?string $selector = null): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $page);

        self::assertSame(200, $client->getResponse()->getStatusCode());
        if ($expected && $selector) {
            self::assertStringContainsString($expected, $crawler->filter($selector)->text());
        }
    }

    public static function provideCases(): iterable
    {
        yield ['/', 'symfony/workflow', '.container-fluid h1'];
        yield ['/tasks'];
        yield ['/articles'];
    }
}
