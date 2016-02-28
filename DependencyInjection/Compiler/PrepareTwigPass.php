<?php

/*
 * This file is part of the GifExceptionBundle Project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 */

namespace Joli\GifExceptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PrepareTwigPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('twig') || !$container->hasDefinition('twig.loader.filesystem')) {
            return;
        }

        $this->addTwigVariable($container);
        $this->overrideTwigTemplateNamespace($container);
    }

    private function addTwigVariable(ContainerBuilder $container)
    {
        $gifs = array();

        $pattern = __DIR__ . '/../../Resources/public/images/*/*.gif';
        foreach (glob($pattern) as $path) {
            $gifs[basename(dirname($path))][] = basename($path);
        }

        $pattern = __DIR__ . '/../../Resources/public/images/*.gif';
        foreach (glob($pattern) as $path) {
            $gifs['other'][] = basename($path);
        }

        $twigDefinition = $container->getDefinition('twig');
        $twigDefinition->addMethodCall('addGlobal', array('fail_gifs', $gifs));
    }

    private function overrideTwigTemplateNamespace(ContainerBuilder $container)
    {
        $templateDir = __DIR__ . '/../../Resources/views';
        $twigFilesystemLoaderDefinition = $container->getDefinition('twig.loader.filesystem');

        $methodCalls = $twigFilesystemLoaderDefinition->getMethodCalls();
        array_unshift($methodCalls, array('addPath', array($templateDir, 'Twig')));

        $twigFilesystemLoaderDefinition->setMethodCalls($methodCalls);
    }
}
