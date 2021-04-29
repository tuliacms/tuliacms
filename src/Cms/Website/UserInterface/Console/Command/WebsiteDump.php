<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Website\Application\Service\DynamicConfigurationDumper;
use Tulia\Cms\Website\Query\Enum\ScopeEnum;
use Tulia\Cms\Website\Query\FinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteDump extends Command
{
    private DynamicConfigurationDumper $dumper;

    public function __construct(DynamicConfigurationDumper $dumper)
    {
        parent::__construct();
        $this->dumper = $dumper;
    }

    protected function configure()
    {
        $this
            ->setName('website:configuration:dump')
            ->setDescription('Dump configured websites to dynamic configuration file.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->dumper->dump();

        $output->writeln('<info>Websites dumped successfully.</info>');

        return Command::SUCCESS;
    }
}
