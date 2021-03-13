<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\UI\CLI\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
use Tulia\Framework\Console\Command\Command;

/**
 * @author Adam Banaszkiewicz
 */
class AssetsPublish extends Command
{
    /**
     * @var AssetsPublisher
     */
    private $assetsPublisher;

    /**
     * @var array
     */
    private $maps;

    /**
     * @var string
     */
    private $rootDir;

    public function __construct(AssetsPublisher $assetsPublisher, array $maps, string $rootDir)
    {
        $this->assetsPublisher = $assetsPublisher;
        $this->maps = $maps;
        $this->rootDir = $rootDir;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('assets:publish')
            ->setDescription('Publish assets to /public directory.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->maps as $source => $target) {
            $sourceHumanized = str_replace($this->rootDir, '', $source);
            $targetHumanized = '/public/assets' . $target;

            if ($this->assetsPublisher->publish($source, $target)) {
                $output->writeln(sprintf('<info>%s => %s</info>', $sourceHumanized, $targetHumanized));
            } else {
                $output->writeln(sprintf('<fg=red>%s => %s</>', $sourceHumanized, $targetHumanized));
            }
        }

        return Command::SUCCESS;
    }
}