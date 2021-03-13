<?php declare(strict_types=1);

use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Tulia\Cms\Installator\Application\Service\Steps\AdminAccountInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\AssetsInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\DatabaseInstallator;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Cms\User\Application\Command\UserStorage;
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

$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/installator' => dirname(__DIR__) . '/views',
]);
