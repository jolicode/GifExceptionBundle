<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle\DependencyInjection\Loader\Configurator;

use Joli\GifExceptionBundle\Command\GifOptimizerCommand;
use Joli\GifExceptionBundle\EventListener\ReplaceImageListener;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('gif_exception.listener.replace_image', ReplaceImageListener::class)
        ->args([
            abstract_arg('gif paths'),
            param('kernel.error_controller'),
            service('assets.packages')->nullOnInvalid(),
        ])
        ->tag('kernel.event_subscriber')
    ;
    $container->services()
        ->set(GifOptimizerCommand::class)
        ->tag('console.command')
    ;
};
