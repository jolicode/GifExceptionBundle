<?php

/*
 * This file is part of the GifExceptionBundle Project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 */

namespace Joli\GifExceptionBundle\DependencyInjection;

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

        $definition = new Definition('Joli\GifExceptionBundle\EventListener\ReplaceImageListener');
        $definition->addTag('kernel.event_listener', array(
            'event' => KernelEvents::RESPONSE,
            'priority' => -1000,
        ));

        $gifs = array();

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

        $definition->addArgument($container->getParameter('twig.exception_listener.controller'));

        if ($container->has('assets.packages')) {
            // New Asset component to generate asset url (SF >=2.8)
            $definition->addArgument(new Reference('assets.packages'));
            $definition->addArgument(null);
        } elseif ($container->has('templating.helper.assets')) {
            // Old way of generating asset url (SF ~2.3)
            $definition->addArgument(null);
            $definition->addArgument(new Reference('templating.helper.assets'));
            $definition->setScope('request');
        }
    }
}
