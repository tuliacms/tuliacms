<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\UserInterface\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\Options\Application\Service\WebsitesOptionsRegistrator;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Website\Ports\Domain\ReadModel\WebsiteFinderScopeEnum;
use Tulia\Cms\Website\Ports\Domain\ReadModel\WebsiteFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class OptionsRegister extends Command
{
    private WebsiteFinderInterface $websiteFinder;
    private WebsitesOptionsRegistrator $optionsRegistrator;

    public function __construct(WebsiteFinderInterface $websiteFinder, WebsitesOptionsRegistrator $optionsRegistrator)
    {
        parent::__construct();
        $this->websiteFinder = $websiteFinder;
        $this->optionsRegistrator = $optionsRegistrator;
    }

    protected function configure(): void
    {
        $this
            ->setName('options:register')
            ->setDescription('Register all available, not registered options in system, for given website.')
            ->addOption(
                'websites',
                'w',
                InputOption::VALUE_OPTIONAL,
                'Website ID list (separated by comma) to which to save options. If empty - register for all available websites.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $websites = $this->fetchWebsites($input->getOption('websites'));

        foreach ($websites as $website) {
            $output->writeln(sprintf('Registering options for website %s...', $website->getId()));
            $this->optionsRegistrator->registerMissingOptionsForWebsite($website->getId());
        }

        $output->writeln('<info>Missing options successfully registered.</info>');

        return Command::SUCCESS;
    }

    private function fetchWebsites(?string $websiteIdSource): Collection
    {
        $websiteIdList = array_filter(explode(',', (string) $websiteIdSource));
        $criteria = [];

        if ($websiteIdList !== []) {
            $criteria = ['id__in' => $websiteIdList];
        }

        return $this->websiteFinder->find($criteria, WebsiteFinderScopeEnum::INTERNAL);
    }
}
