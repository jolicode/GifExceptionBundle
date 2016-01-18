<?php

/*
 * This file is part of the GifExceptionBundle Project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 */

namespace Joli\GifExceptionBundle\Tests\app;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new \Joli\GifExceptionBundle\GifExceptionBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (version_compare(self::VERSION, '2.7.0', '>=')) {
            $loader->load($this->getRootDir() . '/config/config_27.yml');

            return;
        }

        $loader->load($this->getRootDir() . '/config/config.yml');
    }
}
