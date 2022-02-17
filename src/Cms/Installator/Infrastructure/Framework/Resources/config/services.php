<?php declare(strict_types=1);

use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Tulia\Cms\Installator\Application\Service\Steps\AdminAccountInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\AssetsInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\DatabaseInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\InstallationFinisher;
use Tulia\Cms\Installator\Application\Service\Steps\WebsiteInstallator;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
use Tulia\Cms\Shared\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\User\Application\Command\UserStorage;
use Tulia\Cms\Website\Application\Command\WebsiteStorage;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(DatabaseInstallator::class, DatabaseInstallator::class, [
    'arguments' => [
        service(ConfigurationLoader::class),
        parameter('kernel.project_dir'),
    ],
]);

$builder->setDefinition(AssetsInstallator::class, AssetsInstallator::class, [
    'arguments' => [
        service(AssetsPublisher::class),
    ],
]);

$builder->setDefinition(AdminAccountInstallator::class, AdminAccountInstallator::class, [
    'arguments' => [
        service(UserStorage::class),
        service(UuidGeneratorInterface::class),
    ],
]);

$builder->setDefinition(WebsiteInstallator::class, WebsiteInstallator::class, [
    'arguments' => [
        service(WebsiteStorage::class),
        service(UuidGeneratorInterface::class),
    ],
]);

$builder->setDefinition(InstallationFinisher::class, InstallationFinisher::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        parameter('kernel.project_dir'),
    ],
]);

$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/installator' => dirname(__DIR__) . '/views',
]);
