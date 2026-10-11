<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle;

use Joli\GifExceptionBundle\EventListener\ReplaceImageListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class GifExceptionBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$builder->getParameter('kernel.debug')) {
            return;
        }

        $gifs = [];

        foreach (glob($this->getPath() . '/public/images/*/*.gif') ?: [] as $path) {
            $gifs[basename(\dirname($path))][] = basename($path);
        }

        $container->services()
            ->set('gif_exception.listener.replace_image', ReplaceImageListener::class)
            ->args([
                $gifs,
                param('kernel.error_controller'),
                service('assets.packages')->nullOnInvalid(),
            ])
            ->tag('kernel.event_subscriber')
        ;
    }
}
