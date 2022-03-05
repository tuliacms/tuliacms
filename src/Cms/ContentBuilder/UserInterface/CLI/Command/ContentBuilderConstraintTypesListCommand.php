<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\CLI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\ConstraintTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderConstraintTypesListCommand extends Command
{
    protected static $defaultName = 'content-builder:constraint-type:list';
    private ConstraintTypeMappingRegistry $mappingRegistry;

    public function __construct(ConstraintTypeMappingRegistry $mappingRegistry)
    {
        parent::__construct(static::$defaultName);

        $this->mappingRegistry = $mappingRegistry;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [];

        foreach ($this->mappingRegistry->all() as $code => $type) {
            $rows[] = [
                $code,
                $type['classname'],
                $type['label'],
                implode(', ', array_keys($type['modificators'])),
            ];
        }

        (new Table($output))
            ->setHeaders(['code', 'classname', 'label', 'modificators'])
            ->setRows($rows)
            ->render()
        ;

        return Command::SUCCESS;
    }
}
