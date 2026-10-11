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

use function Castor\io;
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
    run('vendor/bin/phpunit');
}

#[AsTask(description: 'Run static analysis')]
function phpstan(): void
{
    run('vendor/bin/phpstan analyse -c phpstan.neon');
}

#[AsTask(description: 'Optimize and resize gifs with gifsicle')]
function optimize(string $dir = 'Resources/public/images', int $width = 145, string $level = '-O3'): void
{
    foreach (glob("{$dir}/*/*.gif") ?: [] as $gif) {
        $before = filesize($gif);
        run(['gifsicle', '-b', $level, "--resize-width={$width}", $gif], quiet: true);
        clearstatcache(true, $gif);
        $after = filesize($gif);

        io()->writeln(sprintf('%s: %d KB -> %d KB (%d%%)', $gif, $before / 1024, $after / 1024, 100 - $after * 100 / $before));
    }
}
