<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle\Tests\Command;

use ImageOptimizer\Exception\CommandNotFound;
use Joli\GifExceptionBundle\Tests\KernelTestCase;

class GifOptimizerCommandTest extends KernelTestCase
{
    private string $prototypeGif;
    private string $testGif;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prototypeGif = __DIR__ . '/original.gif';
        $this->testGif = __DIR__ . '/gifs/test.gif';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        copy($this->prototypeGif, $this->testGif);
    }

    public function testExceptionRaisedForInvalidImageDir(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->getOutputForCommand('jolicode:gifexception:optimize', ['image_dir' => 'foobar']);
    }

    public function testGifIsResizedToExpectedWidth(): void
    {
        [$originalWidth] = getimagesize($this->testGif);

        $expectedWidth = 145;
        $args = ['image_dir' => __DIR__];
        $options = ['--resize_width' => $expectedWidth];

        try {
            $this->getOutputForCommand('jolicode:gifexception:optimize', $args, $options);
        } catch (CommandNotFound $e) {
            $this->markTestSkipped('Gif optimizer tool is not executable');
        }

        clearstatcache(true, $this->testGif);

        [$optimizedWidth] = getimagesize($this->testGif);

        self::assertSame($expectedWidth, $optimizedWidth);
        self::assertNotSame($originalWidth, $optimizedWidth);
    }

    public function testGifIsSmallerFileSize(): void
    {
        $originalSize = filesize($this->testGif);

        $args = ['image_dir' => __DIR__];

        try {
            $this->getOutputForCommand('jolicode:gifexception:optimize', $args);
        } catch (CommandNotFound $e) {
            $this->markTestSkipped('Gif optimizer tool is not executable');
        }

        clearstatcache(true, $this->testGif);

        $optimizedSize = filesize($this->testGif);

        self::assertLessThanOrEqual($originalSize, $optimizedSize);
    }
}
