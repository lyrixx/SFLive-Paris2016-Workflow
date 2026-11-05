<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class OpenGraphTest extends WebTestCase
{
    public function testUrlsAreAbsoluteAndUseTheSchemeOfTheReverseProxy(): void
    {
        $client = static::createClient();

        // 10.0.0.1 is the value of CC_REVERSE_PROXY_IPS in .env.test
        $crawler = $client->request('GET', '/tasks', server: [
            'HTTP_HOST' => 'demo.example',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'REMOTE_ADDR' => '10.0.0.1',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame('https://demo.example/tasks', $this->getTag($crawler, 'og:url'));
        self::assertMatchesRegularExpression('#^https://demo\.example/assets/images/social-preview-[\w-]+\.png$#', $this->getTag($crawler, 'og:image'));
    }

    public function testAnUntrustedClientCannotForgeTheScheme(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks', server: [
            'HTTP_HOST' => 'demo.example',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'REMOTE_ADDR' => '192.0.2.1',
        ]);

        self::assertSame('http://demo.example/tasks', $this->getTag($crawler, 'og:url'));
    }

    public function testTheTitleFollowsThePage(): void
    {
        $client = static::createClient();

        $client->request('POST', '/articles/create', ['title' => 'Test']);
        $crawler = $client->followRedirect();

        self::assertSame('Test · Articles · Symfony Workflow demo', $this->getTag($crawler, 'og:title'));
    }

    private function getTag(Crawler $crawler, string $property): ?string
    {
        return $crawler->filter(\sprintf('meta[property="%s"]', $property))->attr('content');
    }
}
