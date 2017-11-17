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

use ImageOptimizer\Optimizer;
use ImageOptimizer\OptimizerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GifOptimizerCommand extends Command
{
    /**
     * @var string The name of the command
     */
    const COMMAND_NAME = 'jolicode:gifexception:optimize';

    /**
     * @var string
     */
    const DEFAULT_OPTIMIZATION_LEVEL = '-O3';

    /**
     * @var int
     */
    const DEFAULT_WIDTH = 145;

    /**
     * @var Optimizer
     */
    private $optimizer;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
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
            ->addOption('ignore_errors', null, InputOption::VALUE_NONE, 'Would you like to ignore errors?')
            ->setDescription('Optimize gifs')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $ignoreErrors = (bool) $input->getOption('ignore_errors');
        $optimizationLevel = $input->getOption('optimization_level');
        $width = $input->getOption('resize_width');

        $options = [
            'ignore_errors' => $ignoreErrors,
            'gifsicle_options' => ['-b', $optimizationLevel, '--resize-width=' . $width],
        ];

        $factory = new OptimizerFactory($options);
        $this->optimizer = $factory->get('gif');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $imageDir = $input->getArgument('image_dir');

        if (!is_dir($imageDir)) {
            throw new \RuntimeException($imageDir . ' is not a valid directory');
        }

        $pattern = $imageDir . '/*/*.gif';
        foreach (glob($pattern) as $path) {
            $realPath = realpath($path);
            $originalFileSize = filesize($realPath);

            $output->writeln(sprintf('<info>Optimizing image: %s</info>', $realPath));
            $output->writeln(sprintf('<comment>Before: %s</comment>', $this->formatBytes($originalFileSize)));

            $this->optimizer->optimize($realPath);

            // File size information is cached, so make sure we clear this to get the file save difference.
            clearstatcache(true, $realPath);

            $optimizedFileSize = filesize($realPath);

            $output->writeln(sprintf('<comment>After: %s</comment>', $this->formatBytes($optimizedFileSize)));

            $percentage = 100 - (($optimizedFileSize / $originalFileSize) * 100);
            $output->writeln(sprintf('<comment>Saving: %s%%</comment>', round($percentage)));
        }
    }

    /**
     * @param $bytes
     * @param bool $useStandard
     *
     * @return string
     */
    private function formatBytes($bytes, $useStandard = true)
    {
        $unit = $useStandard ? 1024 : 1000;
        if ($bytes <= $unit) {
            return $bytes . ' B';
        }
        $exp = (int) ((log($bytes) / log($unit)));
        $pre = ($useStandard ? 'kMGTPE' : 'KMGTPE');
        $pre = $pre[$exp - 1] . ($useStandard ? '' : 'i');

        return sprintf('%.1f %sB', $bytes / pow($unit, $exp), $pre);
    }
}
