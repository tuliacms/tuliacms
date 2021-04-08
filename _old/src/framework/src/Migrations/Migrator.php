<?php

declare(strict_types=1);

namespace Tulia\Framework\Migrations;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Exception\NoMigrationsFoundWithCriteria;
use Doctrine\Migrations\Exception\NoMigrationsToExecute;
use Doctrine\Migrations\Exception\UnknownMigrationVersion;
use Doctrine\Migrations\MigratorConfiguration;
use Tulia\Framework\Migrations\Exception\EmptyPlanException;
use Tulia\Framework\Migrations\Exception\NoMigrationsToExecuteException;
use Tulia\Framework\Migrations\Exception\UnknownMigrationVersionException;

/**
 * @author Adam Banaszkiewicz
 */
class Migrator
{
    /**
     * @var DependencyFactory
     */
    private $factory;

    public function __construct(DependencyFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @throws EmptyPlanException
     * @throws NoMigrationsToExecuteException
     * @throws UnknownMigrationVersionException
     */
    public function migrate(): void
    {
        $migratorConfiguration = (new MigratorConfiguration())
            ->setDryRun(false)
            ->setTimeAllQueries(false)
            ->setAllOrNothing(true);

        $this->factory->getMetadataStorage()->ensureInitialized();

        $versionAlias = 'latest';

        $migrationRepository = $this->factory->getMigrationRepository();
        if (count($migrationRepository->getMigrations()) === 0) {
            throw NoMigrationsToExecuteException::withVersion($versionAlias);
        }

        try {
            $version = $this->factory->getVersionAliasResolver()->resolveVersionAlias($versionAlias);
        } catch (UnknownMigrationVersion $e) {
            throw UnknownMigrationVersionException::withVersion($versionAlias);
        } catch (NoMigrationsToExecute | NoMigrationsFoundWithCriteria $e) {
            throw NoMigrationsToExecuteException::withVersion($versionAlias);
        }

        $planCalculator = $this->factory->getMigrationPlanCalculator();

        $plan = $planCalculator->getPlanUntilVersion($version);

        if (count($plan) === 0) {
            throw EmptyPlanException::withEmpty();
        }

        $migrator = $this->factory->getMigrator();
        $migrator->migrate($plan, $migratorConfiguration);
    }
}
