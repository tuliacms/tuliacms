<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Database\Connection;
use Tulia\Framework\Database\ConnectionInterface;

$builder->setDefinition(ConnectionInterface::class, Connection::class, [
    'factory' => 'Doctrine\DBAL\DriverManager::getConnection',
    'arguments' => [
        [
            'dbname'   => $_ENV['DATABASE_NAME'] ?? '',
            'user'     => $_ENV['DATABASE_USER'] ?? '',
            'password' => $_ENV['DATABASE_PASS'] ?? '',
            'host'     => $_ENV['DATABASE_HOST'] ?? 'localhost',
            'port'     => $_ENV['DATABASE_PORT'] ?? 3306,
            'driver'   => $_ENV['DATABASE_TYPE'] ?? '',
            'wrapperClass' => Connection::class,
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            ],
        ],
    ],
]);
