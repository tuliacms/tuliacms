<?php declare(strict_types=1);

use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Security\Authorisation\Voter\BackendVoter;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(AccessDecisionManagerInterface::class, AccessDecisionManager::class, [
    'arguments' => [
        tagged('security.voter'),
    ],
]);

$builder->setDefinition(AuthorizationCheckerInterface::class, AuthorizationChecker::class, [
    'arguments' => [
        service(TokenStorageInterface::class),
        service(AuthenticationManagerInterface::class),
        service(AccessDecisionManagerInterface::class),
    ],
]);

$builder->setDefinition(AuthenticatedVoter::class, AuthenticatedVoter::class, [
    'arguments' => [
        service(AuthenticationTrustResolverInterface::class),
    ],
    'tags' => [ tag('security.voter') ],
]);

$builder->setDefinition(BackendVoter::class, BackendVoter::class, [
    'tags' => [ tag('security.voter') ],
]);
