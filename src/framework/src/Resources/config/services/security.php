<?php declare(strict_types=1);

include __DIR__.'/security.csrf.php';
include __DIR__.'/security.core.authentication.php';
include __DIR__.'/security.core.authorisation.php';
include __DIR__.'/security.core.user.php';
include __DIR__.'/security.core.encoder.php';
include __DIR__.'/security.http.php';
include __DIR__.'/security.http.csp.php';
include __DIR__.'/security.http.firewall.php';

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/** @var ContainerBuilderInterface $builder */

/**
 * Default parameters.
 */
$builder->setParameter('security.user_provider.in_memory.users', []);
$builder->setParameter('security.authentication.login_path', null);
