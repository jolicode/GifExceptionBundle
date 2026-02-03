<?php

/*
 * This file is part of the GifExceptionBundle project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\GifExceptionBundle\Command;

use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'gifexception:optimize', description: 'Optimize gifs')]
class GifOptimizerCommand extends Command
{
    private const DEFAULT_OPTIMIZATION_LEVEL = '-O3';
    private const DEFAULT_WIDTH = 145;

    private OptimizerChain $optimizer;

    protected function configure(): void
    {
        $this
            ->addArgument(
                'image_dir',
                InputArgument::OPTIONAL,
                'Location of images',
                __DIR__ . '/../Resources/public/images'
            )
            ->addOption(
                'optimization_level',
                null,
                InputOption::VALUE_OPTIONAL,
                'What level optimization do you require?',
                self::DEFAULT_OPTIMIZATION_LEVEL
            )
            ->addOption(
                'resize_width',
                null,
                InputOption::VALUE_OPTIONAL,
                'Width you would like to resize to?',
                self::DEFAULT_WIDTH
            )
            ->setDescription('Optimize gifs')
        ;

        $this->setHidden(true);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $optimizationLevel = $input->getOption('optimization_level');
        $width = $input->getOption('resize_width');

        $this->optimizer = OptimizerChainFactory::create()
            ->setOptimizers([
                new Gifsicle([
                    '-b',
                    $optimizationLevel,
                    sprintf('--resize-width=%s', (string) $width),
                ]),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $imageDir = $input->getArgument('image_dir');

        if (!\is_string($imageDir) || !is_dir($imageDir)) {
            throw new \RuntimeException('image_dir is not a valid directory');
        }

        $pattern = $imageDir . '/*/*.gif';
        $images = glob($pattern);

        if (!$images) {
            throw new \RuntimeException('No images found in ' . $pattern);
        }

        foreach ($images as $path) {
            $realPath = realpath($path);

            if (!$realPath) {
                throw new \RuntimeException('Could not find ' . $path);
            }

            $originalFileSize = filesize($realPath);

            if (!$originalFileSize) {
                throw new \RuntimeException('Could not get file size for ' . $realPath);
            }

            $output->writeln(\sprintf('<info>Optimizing image: %s</info>', $realPath));
            $output->writeln(\sprintf('<comment>Before: %s</comment>', $this->formatBytes($originalFileSize)));

            $this->optimizer->optimize($realPath);

            // File size information is cached, so make sure we clear this to get the file save difference.
            clearstatcache(true, $realPath);

            $optimizedFileSize = filesize($realPath);

            if (!$optimizedFileSize) {
                throw new \RuntimeException('Could not get file size for ' . $realPath);
            }

            $output->writeln(\sprintf('<comment>After: %s</comment>', $this->formatBytes($optimizedFileSize)));

            $percentage = 100 - (($optimizedFileSize / $originalFileSize) * 100);
            $output->writeln(\sprintf('<comment>Saving: %s%%</comment>', round($percentage)));
        }

        return 0;
    }

    private function formatBytes(int $bytes, bool $useStandard = true): string
    {
        $unit = $useStandard ? 1024 : 1000;
        if ($bytes <= $unit) {
            return $bytes . ' B';
        }
        $exp = (int) (log($bytes) / log($unit));
        $pre = ($useStandard ? 'kMGTPE' : 'KMGTPE');
        $pre = $pre[$exp - 1] . ($useStandard ? '' : 'i');

        return \sprintf('%.1f %sB', $bytes / ($unit ** $exp), $pre);
    }
}
