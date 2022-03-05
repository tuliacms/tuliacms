<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\CLI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderContentTypesListCommand extends Command
{
    protected static $defaultName = 'content-builder:content-type:list';
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        parent::__construct(static::$defaultName);

        $this->configuration = $configuration;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [];

        foreach ($this->configuration->getTypes() as $type) {
            $rows[] = [
                $type,
                $this->configuration->isConfigurable($type) ? 'Yes' : 'No',
                $this->configuration->isMultilingual($type) ? 'Yes' : 'No',
                $this->configuration->getController($type),
                $this->configuration->getLayoutBuilder($type),
            ];
        }

        (new Table($output))
            ->setHeaders(['code', 'configurable', 'multilingual', 'controller', 'layout_builder'])
            ->setRows($rows)
            ->render()
        ;

        return Command::SUCCESS;
    }
}
