<?php

/*
 * This file is part of the GifExceptionBundle Project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 */

namespace Joli\GifExceptionBundle;

use Joli\GifExceptionBundle\DependencyInjection\Compiler\PrepareTwigPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GifExceptionBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PrepareTwigPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }

    public function getParent()
    {
        return 'TwigBundle';
    }
}
