<?php declare(strict_types=1);

use Tulia\Cms\Security\Infrastructure\Framework\Security\Authentication\Provider\UserProvider;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Database\ConnectionInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(UserProvider::class, UserProvider::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
    'tags' => [ tag('security.user_provider') ],
]);


$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'backend/security' => dirname(__DIR__) . '/views/backend',
]);
