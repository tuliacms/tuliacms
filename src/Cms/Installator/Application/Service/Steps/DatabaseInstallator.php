<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\Application\Service\Steps;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory;
use Tulia\Framework\Database\Connection;
use Tulia\Framework\Migrations\Exception\EmptyPlanException;
use Tulia\Framework\Migrations\Exception\NoMigrationsToExecuteException;
use Tulia\Framework\Migrations\Exception\UnknownMigrationVersionException;
use Tulia\Framework\Migrations\Migrator;
use Tulia\Cms\Installator\Application\Exception\UnknownMigrationVersionException as ApplicationUnknownMigrationVersionException;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseInstallator
{
    /**
     * @var ConfigurationLoader
     */
    private $configurationLoader;

    /**
     * @var string
     */
    private $projectDir;

    public function __construct(ConfigurationLoader $configurationLoader, string $projectDir)
    {
        $this->configurationLoader = $configurationLoader;
        $this->projectDir = $projectDir;
    }

    /**
     * @param array $credentials
     * @throws ApplicationUnknownMigrationVersionException
     * @throws Exception
     */
    public function install(array $credentials): void
    {
        $_ENV['DATABASE_PREFIX'] = $credentials['prefix'];

        $connection = DriverManager::getConnection([
            'dbname'   => $credentials['name'],
            'user'     => $credentials['username'],
            'password' => $credentials['password'],
            'host'     => $credentials['host'],
            'port'     => $credentials['port'],
            'driver'   => 'pdo_mysql',
            'wrapperClass' => Connection::class,
            'driverOptions' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            ],
        ]);

        $dc = DependencyFactory::fromConnection($this->configurationLoader, new ExistingConnection($connection));

        try {
            (new Migrator($dc))->migrate();
        } catch (EmptyPlanException | NoMigrationsToExecuteException $e) {
            // Do nothing on those exceptions.
        } catch (UnknownMigrationVersionException $e) {
            throw ApplicationUnknownMigrationVersionException::withUnknownVersion();
        }

        file_put_contents($this->projectDir . '/.env', <<<EOF
APP_ENV=dev
APP_DEBUG=true
DATABASE_HOST={$credentials['host']}
DATABASE_PORT={$credentials['port']}
DATABASE_NAME={$credentials['name']}
DATABASE_USER={$credentials['username']}
DATABASE_PASS={$credentials['password']}
DATABASE_TYPE=pdo_mysql
DATABASE_PREFIX={$credentials['prefix']}

EOF
        );
    }
}
