<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle\EventListener;

use Symfony\Component\Asset\Packages;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ReplaceImageListener implements EventSubscriberInterface
{
    public function __construct(
        /** @var string[][] */
        private array $gifs,
        private string $exceptionController,
        private ?Packages $packages = null
    ) {
    }

    /**
     * Handle the response for exception and replace the little Phantom by a random Gif.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($event->isMainRequest()
            || $event->getRequest()->attributes->get('_controller') !== $this->exceptionController) {
            return;
        }

        $exception = $event->getRequest()->attributes->get('exception');
        // Status code is not set by the exception controller but only by the
        // kernel at the very end.
        // So lets use the status code from the flatten exception instead.
        // Unless it comes from a fatal error handler or exception base class
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = $exception->getCode();
        }

        $dir = $this->getGifDir($statusCode);
        $gif = $this->getRandomGif($dir);
        $url = $this->getGifUrl($dir, $gif);

        $content = $event->getResponse()->getContent();

        $content = preg_replace(
            '@<div class="exception-illustration hidden-xs-down">(.*?)</div>@ims',
            sprintf('<div class="exception-illustration hidden-xs-down" style="opacity:1"><img alt="Exception detected!" src="%s" data-gif style="height:66px" /></div>', $url),
            $content
        );

        $event->getResponse()->setContent($content);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -1000],
        ];
    }

    /**
     * Return the gif folder for the given status code.
     */
    private function getGifDir(int $statusCode): string
    {
        if (\array_key_exists($statusCode, $this->gifs) && \count($this->gifs[$statusCode]) > 0) {
            return (string) $statusCode;
        }

        return 'other';
    }

    /**
     * Return a random gif name for the given directory.
     */
    private function getRandomGif(string $dir): string
    {
        $imageIndex = random_int(0, \count($this->gifs[$dir]) - 1);

        return $this->gifs[$dir][$imageIndex];
    }

    /**
     * Return the url of given gif in the given directory.
     */
    private function getGifUrl(string $dir, string $gif): string
    {
        return $this->generateUrl(sprintf('bundles/gifexception/images/%s/%s', $dir, $gif));
    }

    /**
     * Generate an url with the asset package if available.
     */
    private function generateUrl(string $url): string
    {
        if (null !== $this->packages) {
            return $this->packages->getUrl($url);
        }

        return $url;
    }
}
