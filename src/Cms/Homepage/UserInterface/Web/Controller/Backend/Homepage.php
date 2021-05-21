<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Controller\Backend;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Homepage extends AbstractController
{
    public function index(/*DependencyFactory $dependencyFactory*/): ViewInterface
    {
        /*$input = new ArrayInput([]);

        $dependencyFactory->getMetadataStorage()->ensureInitialized();

        $migratorConfigurationFactory = $dependencyFactory->getConsoleInputMigratorConfigurationFactory();
        $migratorConfiguration        = $migratorConfigurationFactory->getMigratorConfiguration($input);

        $migrationRepository = $dependencyFactory->getMigrationRepository();

        if (count($migrationRepository->getMigrations()) === 0) {
            echo 'No migrations found';
            exit;
        }

        $version = $dependencyFactory->getVersionAliasResolver()->resolveVersionAlias('latest');
        $planCalculator = $dependencyFactory->getMigrationPlanCalculator();

        $plan = $planCalculator->getPlanUntilVersion($version);

        if (count($plan) === 0) {
            echo 'No plan for migration';
            exit;
        }

        $dependencyFactory->getLogger()->notice(
            'Migrating' . ($migratorConfiguration->isDryRun() ? ' (dry-run)' : '') . ' {direction} to {to}',
            [
                'direction' => $plan->getDirection(),
                'to' => (string) $version,
            ]
        );

        $migrator = $dependencyFactory->getMigrator();
        $sql      = $migrator->migrate($plan, $migratorConfiguration);
        dump($plan, $sql);

        exit;*/
        return $this->view('@backend/homepage/homepage.tpl');
    }
}
