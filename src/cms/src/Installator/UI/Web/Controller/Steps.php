<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\UI\Web\Controller;

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Doctrine\Migrations\DependencyFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Framework\Database\Connection;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\NotFoundHttpException;
use Tulia\Framework\Migrations\Exception\EmptyPlanException;
use Tulia\Framework\Migrations\Exception\NoMigrationsToExecuteException;
use Tulia\Framework\Migrations\Exception\UnknownMigrationVersionException;
use Tulia\Framework\Migrations\Migrator;

/**
 * @author Adam Banaszkiewicz
 */
class Steps extends AbstractInstallationController
{
    /**
     * @var ConfigurationLoader
     */
    private $configurationLoader;

    public function __construct(ConfigurationLoader $configurationLoader)
    {
        $this->configurationLoader = $configurationLoader;
    }

    public function prepare(Request $request): JsonResponse
    {
        if ($this->stepFinished($request, 'preinstall') === false) {
            throw new NotFoundHttpException('Please finish preinstall step first.');
        }

        $credentials = $request->getSession()->get('installator.db');

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
            return new JsonResponse([
                'message' => 'Unknown migration version. Please verify this situation.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $credentials = $request->getSession()->get('installator.db');
        $root = $this->getParameter('kernel.project_dir');

        file_put_contents($root . '/.env', <<<EOF
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

        return new JsonResponse();
    }
}
