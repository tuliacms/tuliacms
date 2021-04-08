<?php declare(strict_types=1);

use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/** @var ContainerBuilderInterface $builder */

$configFile = $builder->getParameter('kernel.project_dir').'/config/services.php';

if (is_file($configFile)) {
    include $configFile;
}

include __DIR__.'/services/kernel.php';
include __DIR__.'/services/session.php';
include __DIR__.'/services/web.php';
include __DIR__.'/services/database.php';
include __DIR__.'/services/routing.php';
include __DIR__.'/services/profiler.php';
include __DIR__.'/services/security.php';
include __DIR__.'/services/twig.php';
include __DIR__.'/services/templating.php';
include __DIR__.'/services/theme.php';
include __DIR__.'/services/theme.customizer.php';
include __DIR__.'/services/hooking.php';
include __DIR__.'/services/stdlib.php';
include __DIR__.'/services/translation.php';
include __DIR__.'/services/form.php';
include __DIR__.'/services/validator.php';
include __DIR__.'/services/command_bus.php';
include __DIR__.'/services/shortcode.php';
include __DIR__.'/services/widget.php';
include __DIR__.'/services/console.php';
include __DIR__.'/services/migrations.php';
