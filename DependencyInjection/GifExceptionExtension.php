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

use Joli\GifExceptionBundle\Command\GifOptimizerCommand;
use Joli\GifExceptionBundle\EventListener\ReplaceImageListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\KernelEvents;

class GifExceptionExtension extends Extension implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!$container->getParameter('kernel.debug')) {
            return;
        }

        $definition = new Definition(GifOptimizerCommand::class);
        $definition->addTag('console.command', [
            'command' => GifOptimizerCommand::COMMAND_NAME, // Allow lazy loading
        ]);
        $container->setDefinition(GifOptimizerCommand::class, $definition);

        $definition = new Definition(ReplaceImageListener::class);
        $definition->addTag('kernel.event_listener', [
            'event' => KernelEvents::RESPONSE,
            'priority' => -1000,
        ]);

        $gifs = [];

        $pattern = __DIR__ . '/../Resources/public/images/*/*.gif';
        foreach (glob($pattern) as $path) {
            $gifs[basename(dirname($path))][] = basename($path);
        }

        $pattern = __DIR__ . '/../Resources/public/images/other/*.gif';
        foreach (glob($pattern) as $path) {
            $gifs['other'][] = basename($path);
        }

        // Set first argument. Next ones will be added by the compiler pass.
        $definition->addArgument($gifs);
        $container->setDefinition('gif_exception.listener.replace_image', $definition);
    }

    /**
     * This CompilerPassInterface method completes the listener definition with
     * the parameter and service coming from other bundles. It allows our
     * bundle to be registered anytime before or after others.
     *
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('kernel.debug')) {
            return;
        }

        $definition = $container->getDefinition('gif_exception.listener.replace_image');

        // $container->setArgument($index, ...) was added in Symfony 3.3
        $arguments = [
            $definition->getArgument(0),
            $container->getParameter('twig.exception_listener.controller'),
        ];

        if ($container->has('assets.packages')) {
            // New Asset component to generate asset url (SF >=2.8)
            $arguments[] = new Reference('assets.packages');
            $arguments[] = null;
        } elseif ($container->has('templating.helper.assets')) {
            // Old way of generating asset url (SF ~2.3)
            // To remove when compatibility with Symfony 2.7 is dropped
            $arguments[] = null;
            $arguments[] = new Reference('templating.helper.assets');
            $definition->setScope('request');
        }

        $definition->setArguments($arguments);
    }
}
