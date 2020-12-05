<?php declare(strict_types=1);

use Symfony\Component\Security\Core\User\ChainUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(UserCheckerInterface::class, UserChecker::class);

$builder->setDefinition(UserProviderInterface::class, ChainUserProvider::class, [
    'arguments' => [
        tagged('security.user_provider'),
    ],
]);
