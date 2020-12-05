<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Cms\Activity\Application\Command\ActivityStorage;
use Tulia\Cms\Activity\Infrastructure\Cms\Dashboard\Widget\ActivityWidget;
use Tulia\Cms\Activity\Infrastructure\Persistence\Command\RepositoryInterface;
use Tulia\Cms\Activity\Infrastructure\Persistence\Command\DbalRepository;
use Tulia\Cms\Activity\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Cms\Activity\Infrastructure\Persistence\Query\QueryInterface;
use Tulia\Cms\Activity\Query\Finder;
use Tulia\Cms\Activity\Query\FinderInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Framework\Database\ConnectionInterface;

$builder->setDefinition(RepositoryInterface::class, DbalRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(ActivityStorage::class, ActivityStorage::class, [
    'arguments' => [
        service(RepositoryInterface::class),
        service(CurrentWebsiteInterface::class),
        service(UuidGeneratorInterface::class),
        service(HydratorInterface::class),
    ],
]);

$builder->setDefinition(ActivityWidget::class, ActivityWidget::class, [
    'arguments' => [
        service(FinderInterface::class),
    ],
    'tags' => [ tag('dashboard.widget') ],
]);

$builder->setDefinition(FinderInterface::class, Finder::class, [
    'arguments' => [
        service(QueryInterface::class),
        service(CurrentWebsiteInterface::class),
        service(HydratorInterface::class),
    ],
]);

$builder->setDefinition(QueryInterface::class, DbalQuery::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);


$builder->mergeParameter('templating.paths', [
    'cms/activity' => dirname(__DIR__) . '/views/frontend',
    'backend/activity' => dirname(__DIR__) . '/views/backend',
]);

