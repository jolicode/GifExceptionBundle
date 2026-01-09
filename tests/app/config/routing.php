<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('test_routing_error', '/error-{status}')
        ->requirements(['status' => '\d+'])
        ->defaults(['_controller' => 'Joli\GifExceptionBundle\Tests\app\src\TestController::errorAction'])
    ;

    if (file_exists(__DIR__ . '/../../vendor/symfony/web-profiler-bundle/Resources/config/routing/wdt.php')) {
        $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.php')
            ->prefix('/_wdt')
        ;
        $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.php')
            ->prefix('/_profiler')
        ;
    } else {
        $routes->import('@WebProfilerBundle/Resources/config/routing/wdt.xml')
            ->prefix('/_wdt')
        ;
        $routes->import('@WebProfilerBundle/Resources/config/routing/profiler.xml')
            ->prefix('/_profiler');
    }
};
