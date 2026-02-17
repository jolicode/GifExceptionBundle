<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Castor\Attribute\AsTask;

use function Castor\run;

#[AsTask(description: 'Fix PHP CS')]
function cs(): void
{
    run('vendor/bin/php-cs-fixer fix --verbose');
}

#[AsTask(name: 'cs:dry-run', description: 'Test if PHP CS is correct')]
function cs_dry_run(): void
{
    run('vendor/bin/php-cs-fixer fix --verbose --dry-run');
}

#[AsTask(description: 'Run the test suite')]
function test(): void
{
    run('vendor/bin/simple-phpunit');
}

#[AsTask(description: 'Run static analysis')]
function phpstan(): void
{
    run('vendor/bin/phpstan analyse -c phpstan.neon');
}
