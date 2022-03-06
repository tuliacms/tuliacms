<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\CLI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBuilderFieldTypeInfoCommand extends Command
{
    protected static $defaultName = 'content-builder:field-type:info';
    private FieldTypeMappingRegistry $mappingRegistry;

    public function __construct(FieldTypeMappingRegistry $mappingRegistry)
    {
        parent::__construct(static::$defaultName);

        $this->mappingRegistry = $mappingRegistry;
    }

    protected function configure()
    {
        $this->addArgument('code', InputArgument::REQUIRED, 'Field Type to debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($this->mappingRegistry->hasType($input->getArgument('code')) === false) {
            $io->warning(sprintf('Field Types "%s" not exists.', $input->getArgument('code')));
            return Command::FAILURE;
        }

        $type = $this->mappingRegistry->get($input->getArgument('code'));

        $io->title(sprintf('Informations about "%s" Field Type', $input->getArgument('code')));

        $io->definitionList(
            ['Code' => $input->getArgument('code')],
            ['Label' => $type['label']],
            ['Classname' => $type['classname']],
            ['Handler' => $type['handler'] ?? "''"],
            ['Builder' => $type['builder'] ?? "''"],
            ['Exclude for types' => $type['exclude_for_types'] ? implode(', ', $type['exclude_for_types']) : '[]'],
            ['Only for types' => $type['only_for_types'] ? implode(', ', $type['only_for_types']) : '[]'],
            ['Flags' => $type['flags'] ? implode(', ', $type['flags']) : '[]'],
        );

        $io->title(sprintf('Constraints of "%s" Field Type', $input->getArgument('code')));

        if ($type['constraints'] === []) {
            $io->info('This Field type has no Constraints.');
        }

        foreach ($type['constraints'] as $code => $constraint) {
            $definition = [
                '====== Constraint info ======',
                ['Code' => $code],
                ['Label' => $constraint['label']],
                ['Classname' => $constraint['classname']],
                ['Help text' => $constraint['help_text']],
            ];

            foreach ($constraint['modificators'] as $modificatorCode => $modificator) {
                $definition[] = sprintf('====== "%s" modificator of constraint ======', $modificatorCode);
                $definition[] = ['Type' => $modificator['type']];
                $definition[] = ['Label' => $modificator['label']];
                $definition[] = ['Value' => $modificator['value'] ?? "''"];
            }

            $io->definitionList(...$definition);
        }

        $io->title(sprintf('Configuration options of "%s" Field Type', $input->getArgument('code')));

        if ($type['configuration'] === []) {
            $io->info('This Field type has no Configuration options.');
        }

        foreach ($type['configuration'] as $code => $config) {
            $io->definitionList(
                '====== Configuration info ======',
                ['Code' => $code],
                ['type' => $config['type']],
                ['Required' => $config['required'] ? 'Yes' : 'No'],
                ['Label' => $config['label']],
                ['Help text' => $config['help_text'] ?? "''"],
                ['Placeholder' => $config['placeholder'] ?? "''"],
                ['Choices provider' => $config['choices_provider'] ?? "''"],
                ['Choices' => $config['choices'] ? implode(', ', $config['choices']) : '[]'],
            );
        }

        return Command::SUCCESS;
    }
}
