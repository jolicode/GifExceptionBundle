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
use Joli\GifExceptionBundle\Command\GifOptimizerCommand;
use Joli\GifExceptionBundle\Tests\WebTestCase;

class GifOptimizerCommandTest extends WebTestCase
{
    private $prototypeGif;
    private $testGif;

    public function setUp()
    {
        parent::setUp();

        $this->prototypeGif = __DIR__ . '/original.gif';
        $this->testGif = __DIR__ . '/gifs/test.gif';
    }

    public function tearDown()
    {
        parent::tearDown();
        copy($this->prototypeGif, $this->testGif);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionRaisedForInvalidImageDir()
    {
        $command = new GifOptimizerCommand();
        $this->getOutputForCommand($command, GifOptimizerCommand::COMMAND_NAME, ['image_dir' => 'foobar']);
    }

    public function testGifIsResizedToExpectedWidth()
    {
        list($originalWidth) = getimagesize($this->testGif);

        $expectedWidth = 145;
        $command = new GifOptimizerCommand();
        $args = ['image_dir' => __DIR__];
        $options = ['resize_width' => $expectedWidth];

        try {
            $this->getOutputForCommand($command, GifOptimizerCommand::COMMAND_NAME, $args, $options);
        } catch (CommandNotFound $e) {
            $this->markTestSkipped(sprintf('Gif optimizer tool is not executable'));
        }

        clearstatcache(true, $this->testGif);

        list($optimizedWidth) = getimagesize($this->testGif);

        self::assertEquals($expectedWidth, $optimizedWidth);
        self::assertNotEquals($originalWidth, $optimizedWidth);
    }

    public function testGifIsSmallerFileSize()
    {
        $originalSize = filesize($this->testGif);

        $command = new GifOptimizerCommand();
        $args = ['image_dir' => __DIR__];

        try {
            $this->getOutputForCommand($command, GifOptimizerCommand::COMMAND_NAME, $args);
        } catch (CommandNotFound $e) {
            $this->markTestSkipped(sprintf('Gif optimizer tool is not executable'));
        }

        clearstatcache(true, $this->testGif);

        $optimizedSize = filesize($this->testGif);

        self::assertLessThanOrEqual($originalSize, $optimizedSize);
    }
}
