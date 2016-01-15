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
        self::assertFalse(strpos($response->getContent(), '<img alt="Gif Exception"'));
    }

    /**
     * @test
     */
    public function it_displays_gif_on_exception_page_if_the_bundle_is_enabled()
    {
        $kernel = new \Joli\GifExceptionBundle\Tests\app\AppKernel('dev', true);
        $kernel->boot();

        $request = Request::create('/');
        $response = $kernel->handle($request);

        self::assertSame(404, $response->getStatusCode());
        self::assertNotFalse(strpos($response->getContent(), '<img alt="Gif Exception"'));
    }

    /**
     * @test
     */
    public function it_adds_gifs_in_twig_global_variables()
    {
        $kernel = new \Joli\GifExceptionBundle\Tests\app\AppKernel('dev', true);
        $kernel->boot();

        /** @var \Twig_Environment $twig */
        $twig = $kernel->getContainer()->get('twig');
        $globals = $twig->getGlobals();

        self::assertArrayHasKey('fail_gifs', $globals);
        self::assertNotEmpty($globals['fail_gifs']);
    }
}
