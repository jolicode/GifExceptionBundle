#!/usr/bin/env php
<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Joli\GifExceptionBundle\Command\GifOptimizerCommand;
use Symfony\Component\Console\Application;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $loader = require __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
    $loader = require __DIR__ . '/../../../../vendor/autoload.php';
} else {
    throw new RuntimeException('Unable to load autoloader.');
}

$application = new Application('gifexception');dump($application);
$application->addCommand(new GifOptimizerCommand());
$application
    ->setDefaultCommand('gifexception:optimize', true)
    ->run()
;
