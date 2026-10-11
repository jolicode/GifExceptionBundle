<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle\Tests;

use PHPUnit\Framework\TestCase;

class GifsTest extends TestCase
{
    public function testAllGifsAreResized(): void
    {
        $gifs = glob(__DIR__ . '/../Resources/public/images/*/*.gif');

        self::assertNotEmpty($gifs);

        foreach ($gifs as $gif) {
            [$width] = getimagesize($gif);

            self::assertSame(145, $width, basename($gif) . ' is not resized, run `castor optimize`');
        }
    }
}
