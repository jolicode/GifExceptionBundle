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

use Joli\GifExceptionBundle\Tests\app\src\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

// if you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
// umask(0000);
set_time_limit(0);

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__ . '/../vendor/autoload.php';

$args = $_SERVER['argv'];

// Strip application name
array_shift($args);

// Prepend command name
array_unshift($args, 'jolicode:gifexception:optimize');

// Prepend application name (ArgvInput strips it again so needs to be here)
array_unshift($args, __DIR__ . '/optimizer.php');

$input = new ArgvInput($args);
$kernel = new Kernel('dev', false);
$application = new Application($kernel);
$application->run($input);
