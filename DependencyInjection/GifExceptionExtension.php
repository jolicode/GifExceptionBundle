<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class GifExceptionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        if (!$container->getParameter('kernel.debug')) {
            return;
        }

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.php');

        $gifs = [];

        $pattern = __DIR__ . '/../Resources/public/images/*/*.gif';
        $images = glob($pattern);

        if ($images) {
            foreach ($images as $path) {
                $gifs[basename(\dirname($path))][] = basename($path);
            }
        }

        $pattern = __DIR__ . '/../Resources/public/images/other/*.gif';
        $images = glob($pattern);

        if ($images) {
            foreach ($images as $path) {
                $gifs['other'][] = basename($path);
            }
        }

        $container->getDefinition('gif_exception.listener.replace_image')->replaceArgument(0, $gifs);
    }
}
