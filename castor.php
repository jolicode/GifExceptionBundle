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

#[AsTask(description: 'Fix CS', aliases: ['cs'])]
function cs(bool $dryRun = false): void
{
    $command = 'vendor/bin/php-cs-fixer fix --verbose';
    
    if ($dryRun) {
        $command .= ' --dry-run';
    }
    
    run($command);
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
