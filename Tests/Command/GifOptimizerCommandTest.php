<?php

/*
 * This file is part of the GifExceptionBundle Project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
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
        $this->getOutputForCommand($command, GifOptimizerCommand::COMMAND_NAME, array('image_dir' => 'foobar'));
    }

    public function testGifIsResizedToExpectedWidth()
    {
        list($originalWidth) = getimagesize($this->testGif);

        $expectedWidth = 145;
        $command = new GifOptimizerCommand();
        $args = array('image_dir' => __DIR__);
        $options = array('resize_width' => $expectedWidth);

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
        $args = array('image_dir' => __DIR__);

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
