<?php

/*
 * This file is part of the GifExceptionBundle Project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 */

namespace Joli\GifExceptionBundle\Tests\src;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestController
{
    public function error404Action()
    {
        throw new NotFoundHttpException();
    }

    public function error418Action()
    {
        throw new HttpException(418);
    }
}
