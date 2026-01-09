<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle\Tests\app\src;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TestController
{
    public function errorAction(Request $request): void
    {
        $statusCode = $request->attributes->getInt('status', 404);

        throw new HttpException($statusCode, \sprintf('This is HTTP %s error page!', $statusCode));
    }
}
