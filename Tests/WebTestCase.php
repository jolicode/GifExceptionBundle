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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * @var Application An Application
     */
    protected $application;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->application = new Application($kernel);
    }

    /**
     * Get the output for a Command.
     *
     * @param Command $command     The Command to run
     * @param string  $commandName
     * @param array   $args
     * @param array   $options
     *
     * @return string The output
     */
    protected function getOutputForCommand($command, $commandName, array $args = [], array $options = [])
    {
        if (empty($args)) {
            $args = ['command' => $commandName];
        }

        $this->application->add($command);
        $applicationCommand = $this->application->find($commandName);

        $commandTester = new CommandTester($applicationCommand);
        $commandTester->execute($args, $options);

        return $commandTester->getDisplay();
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        return new AppKernel(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }
}
