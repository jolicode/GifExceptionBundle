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
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;

class ReplaceImageListener
{
    const IMAGES_DIR = '../../Resources/public/images';

    /** @var string[][] */
    private $gifs;

    /** @var string */
    private $exceptionController;

    /** @var Packages */
    private $packages;

    /** @var CoreAssetsHelper */
    private $coreAssetsHelper;

    /**
     * @param string[][]       $gifs
     * @param string           $exceptionController
     * @param Packages         $packages
     * @param CoreAssetsHelper $coreAssetsHelper
     */
    public function __construct(array $gifs, $exceptionController, Packages $packages = null, CoreAssetsHelper $coreAssetsHelper = null)
    {
        $this->gifs = $gifs;
        $this->exceptionController = $exceptionController;
        $this->packages = $packages;
        $this->coreAssetsHelper = $coreAssetsHelper;
    }

    /**
     * Handle the response for exception and replace the little Phantom by a random Gif.
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->isMasterRequest()
            || $event->getRequest()->attributes->get('_controller') !== $this->exceptionController) {
            return;
        }

        // Status code is not set by the exception controller but only by the
        // kernel at the very end.
        // So lets use the status code from the flatten exception instead.
        $statusCode = $event->getRequest()->attributes->get('exception')->getStatusCode();

        $dir = $this->getGifDir($statusCode);
        $gif = $this->getRandomGif($dir);
        $url = $this->getGifUrl($dir, $gif);

        $content = $event->getResponse()->getContent();

        if (version_compare(Kernel::VERSION, '3.2', '<')) {
            $content = preg_replace(
                '/<img alt="Exception detected!" src=".*" \/>/',
                sprintf('<img alt="Exception detected!" src="%s" data-gif style="width:145px" />', $url),
                $content
            );
        } elseif (version_compare(Kernel::VERSION, '3.3', '<')) {
            $content = preg_replace(
                '@<svg xmlns="http://www.w3.org/2000/svg" width="112"(.*?)</svg>@ims',
                sprintf('<img alt="Exception detected!" src="%s" data-gif style="width:145px" />', $url),
                $content
            );
        } else {
            $content = preg_replace(
                '@<div class="exception-illustration hidden-xs-down">(.*?)</div>@ims',
                sprintf('<div class="exception-illustration hidden-xs-down" style="opacity:1"><img alt="Exception detected!" src="%s" data-gif style="height:66px" /></div>', $url),
                $content
            );
        }

        $event->getResponse()->setContent($content);
    }

    /**
     * Return the gif folder for the given status code.
     *
     * @param int $statusCode
     *
     * @return string
     */
    private function getGifDir($statusCode)
    {
        if (array_key_exists($statusCode, $this->gifs) && count($this->gifs[$statusCode]) > 0) {
            return $statusCode;
        }

        return 'other';
    }

    /**
     * Return a random gif name for the given directory.
     *
     * @param string $dir
     *
     * @return string
     */
    private function getRandomGif($dir)
    {
        $imageIndex = mt_rand(0, count($this->gifs[$dir]) - 1);

        return $this->gifs[$dir][$imageIndex];
    }

    /**
     * Return a the url of given gif in the given directory.
     *
     * @param string $dir
     * @param string $gif
     *
     * @return string
     */
    private function getGifUrl($dir, $gif)
    {
        return $this->generateUrl(sprintf('bundles/gifexception/images/%s/%s', $dir, $gif));
    }

    /**
     * Generate an url in both Symfony 2 and Symfony 3+ compatible ways.
     *
     * @param string $url
     *
     * @return string
     */
    private function generateUrl($url)
    {
        if ($this->packages) {
            return $this->packages->getUrl($url);
        }

        if ($this->coreAssetsHelper) {
            // To remove when compatibility with Symfony 2.7 is dropped
            return $this->coreAssetsHelper->getUrl($url);
        }

        return $url;
    }
}
