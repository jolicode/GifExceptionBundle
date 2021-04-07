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

use Joli\GifExceptionBundle\Tests\app\AppKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

abstract class KernelTestCase extends BaseKernelTestCase
{
    /**
     * @var ApplicationTester An Application test
     */
    protected $tester;

    protected function setUp(): void
    {
        self::bootKernel();

        $application = new Application(static::$kernel);
        $application->setCatchExceptions(false);
        $application->setAutoExit(false);

        $this->tester = new ApplicationTester($application);
    }

    /**
     * Get the output for a Command.
     */
    protected function getOutputForCommand(string $commandName, array $args = [], array $options = [])
    {
        $this->tester->run(array_merge(['command' => $commandName], $args, $options));

        return $this->tester->getDisplay();
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        return new AppKernel(
            $options['environment'] ?? 'test',
            $options['debug'] ?? true
        );
    }
}
