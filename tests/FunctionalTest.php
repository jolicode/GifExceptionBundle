<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle\Tests;

use Joli\GifExceptionBundle\Tests\app\src\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class FunctionalTest extends WebTestCase
{
    public function testItDoesNotDisplayGifOnExceptionPageIfTheBundleIsNotEnabled(): void
    {
        $client = static::createClient(['environment' => 'prod', 'debug' => true]);

        $crawler = $client->request('GET', '/error-404');
        $response = $client->getResponse();

        self::assertSame(404, $response->getStatusCode());

        $image = $this->getImage($response->getContent());

        self::assertFalse($image->hasAttribute('data-gif'));
    }

    public function testItDisplaysGifOnExceptionPageIfTheBundleIsEnabled(): void
    {
        $client = static::createClient(['environment' => 'dev', 'debug' => true]);

        $crawler = $client->request('GET', '/error-404');
        $response = $client->getResponse();

        self::assertSame(404, $response->getStatusCode());

        $image = $this->getImage($response->getContent());

        self::assertTrue($image->hasAttribute('data-gif'), 'Image was not replaced.');
        self::assertStringMatchesFormat('%s/gifexception/images/404/%s.gif', $image->getAttribute('src'));

        $crawler = $client->request('GET', '/error-418');
        $response = $client->getResponse();

        self::assertSame(418, $response->getStatusCode());

        $image = $this->getImage($response->getContent());

        self::assertTrue($image->hasAttribute('data-gif'));
        self::assertStringMatchesFormat('%s/gifexception/images/other/%s.gif', $image->getAttribute('src'));
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        return new Kernel(
            $options['environment'] ?? 'test',
            $options['debug'] ?? true
        );
    }

    private function getImage(string $content): \DOMElement
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($content); // svg throw a warning
        $xpath = new \DOMXPath($dom);

        // SF < 3.2 and image replaced
        $image = $xpath->query('//img[@alt="Exception detected!"]')->item(0);

        if (!$image) {
            // SF < 3.3
            $image = $xpath->query('//svg[@width="112"]')->item(0);
        }

        if (!$image) {
            // SF >= 3.3
            $image = $xpath->query('//div[contains(@class, "exception-illustration")]/svg')->item(0);
        }

        $this->assertNotNull($image);

        return $image;
    }
}
