<?php declare(strict_types=1);

use Doctrine\Migrations\Configuration\Connection\ConnectionLoader;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\CurrentCommand;
use Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\ListCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand;
use Doctrine\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Tulia\Framework\Database\ConnectionInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Migrations\Configuration\RuntimeConfiguration;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(ConfigurationLoader::class, RuntimeConfiguration::class, [
    'arguments' => [
        parameter('kernel.project_dir'),
        parameter('migrations.paths'),
        parameter('kernel.modules')
    ],
]);

$builder->setDefinition(ConnectionLoader::class, ExistingConnection::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(DependencyFactory::class, ExistingConfiguration::class, [
    'factory' => [ DependencyFactory::class, 'fromConnection' ],
    'arguments' => [
        service(ConfigurationLoader::class),
        service(ConnectionLoader::class),
    ],
]);

$builder->setDefinition(StatusCommand::class, StatusCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:status', ],
    'tags' => [ tag_console_command('doctrine:migrations:status') ],
]);

$builder->setDefinition(ExecuteCommand::class, ExecuteCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:execute', ],
    'tags' => [ tag_console_command('doctrine:migrations:execute') ],
]);

$builder->setDefinition(MigrateCommand::class, MigrateCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:migrate', ],
    'tags' => [ tag_console_command('doctrine:migrations:migrate') ],
]);

$builder->setDefinition(CurrentCommand::class, CurrentCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:current', ],
    'tags' => [ tag_console_command('doctrine:migrations:current') ],
]);

$builder->setDefinition(DumpSchemaCommand::class, DumpSchemaCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:dump-schema', ],
    'tags' => [ tag_console_command('doctrine:migrations:dump-schema') ],
]);

$builder->setDefinition(GenerateCommand::class, GenerateCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:generate', ],
    'tags' => [ tag_console_command('doctrine:migrations:generate') ],
]);

$builder->setDefinition(LatestCommand::class, LatestCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:latest', ],
    'tags' => [ tag_console_command('doctrine:migrations:latest') ],
]);

$builder->setDefinition(ListCommand::class, ListCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:list', ],
    'tags' => [ tag_console_command('doctrine:migrations:list') ],
]);

$builder->setDefinition(RollupCommand::class, RollupCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:rollup', ],
    'tags' => [ tag_console_command('doctrine:migrations:rollup') ],
]);

$builder->setDefinition(SyncMetadataCommand::class, SyncMetadataCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:sync-metadata-storage', ],
    'tags' => [ tag_console_command('doctrine:migrations:sync-metadata-storage') ],
]);

$builder->setDefinition(UpToDateCommand::class, UpToDateCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:up-to-date', ],
    'tags' => [ tag_console_command('doctrine:migrations:up-to-date') ],
]);

$builder->setDefinition(VersionCommand::class, VersionCommand::class, [
    'arguments' => [ service(DependencyFactory::class), 'doctrine:migrations:version', ],
    'tags' => [ tag_console_command('doctrine:migrations:version') ],
]);


$builder->mergeParameter('migrations.paths', []);
