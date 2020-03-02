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
    private $prototypeGif;
    private $testGif;

    public function setUp(): void
    {
        parent::setUp();

        $this->prototypeGif = __DIR__ . '/original.gif';
        $this->testGif = __DIR__ . '/gifs/test.gif';
    }

    public function tearDown(): void
    {
        parent::tearDown();
        copy($this->prototypeGif, $this->testGif);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionRaisedForInvalidImageDir(): void
    {
        $this->getOutputForCommand('jolicode:gifexception:optimize', ['image_dir' => 'foobar']);
    }

    public function testGifIsResizedToExpectedWidth()
    {
        list($originalWidth) = getimagesize($this->testGif);

        $expectedWidth = 145;
        $args = ['image_dir' => __DIR__];
        $options = ['--resize_width' => $expectedWidth];

        try {
            $this->getOutputForCommand('jolicode:gifexception:optimize', $args, $options);
        } catch (CommandNotFound $e) {
            $this->markTestSkipped(sprintf('Gif optimizer tool is not executable'));
        }

        clearstatcache(true, $this->testGif);

        list($optimizedWidth) = getimagesize($this->testGif);

        self::assertSame($expectedWidth, $optimizedWidth);
        self::assertNotSame($originalWidth, $optimizedWidth);
    }

    public function testGifIsSmallerFileSize()
    {
        $originalSize = filesize($this->testGif);

        $args = ['image_dir' => __DIR__];

        try {
            $this->getOutputForCommand('jolicode:gifexception:optimize', $args);
        } catch (CommandNotFound $e) {
            $this->markTestSkipped(sprintf('Gif optimizer tool is not executable'));
        }

        clearstatcache(true, $this->testGif);

        $optimizedSize = filesize($this->testGif);

        self::assertLessThanOrEqual($originalSize, $optimizedSize);
    }
}
