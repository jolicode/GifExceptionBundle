<?php

/*
 * This file is part of the GifExceptionBundle Project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 */

namespace Joli\GifExceptionBundle\Tests;

use Symfony\Component\HttpFoundation\Request;

class FunctionalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_does_not_display_gif_on_exception_page_if_the_bundle_is_not_enabled()
    {
        $kernel = new \Joli\GifExceptionBundle\Tests\app\AppKernel('prod', true);
        $kernel->boot();

        $request = Request::create('/');
        $response = $kernel->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $image = $this->getImage($response->getContent());

        self::assertFalse($image->hasAttribute('data-gif'));
    }

    /**
     * @test
     */
    public function it_displays_gif_on_exception_page_if_the_bundle_is_enabled()
    {
        $kernel = new \Joli\GifExceptionBundle\Tests\app\AppKernel('dev', true);
        $kernel->boot();

        $request = Request::create('/error-404');
        $response = $kernel->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $image = $this->getImage($response->getContent());

        self::assertTrue($image->hasAttribute('data-gif'));
        self::assertStringMatchesFormat('%s/gifexception/images/404/%s.gif', $image->getAttribute('src'));

        $request = Request::create('/error-418');
        $response = $kernel->handle($request);

        self::assertSame(418, $response->getStatusCode());

        $image = $this->getImage($response->getContent());

        self::assertTrue($image->hasAttribute('data-gif'));
        self::assertStringMatchesFormat('%s/gifexception/images/other/%s.gif', $image->getAttribute('src'));
    }

    /**
     * @param $content
     *
     * @return \DOMElement
     */
    private function getImage($content)
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($content);
        $xpath = new \DomXpath($dom);

        return $xpath->query('//img[@alt="Exception detected!"]')->item(0);
    }
}
