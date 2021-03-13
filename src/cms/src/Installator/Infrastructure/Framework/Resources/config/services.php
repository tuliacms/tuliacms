<?php declare(strict_types=1);

use Doctrine\Migrations\Configuration\Migration\ConfigurationLoader;
use Tulia\Cms\Installator\Application\Service\Steps\AssetsInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\DatabaseInstallator;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
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

$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/installator' => dirname(__DIR__) . '/views',
]);
