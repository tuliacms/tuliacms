<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Component\CommandBus\CommandBus;
use Tulia\Component\CommandBus\CommandBusInterface;
use Tulia\Component\CommandBus\Locator\ArrayLocator;
use Tulia\Component\CommandBus\Locator\LocatorInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/*$builder->setDefinition(CommandBusInterface::class, CommandBus::class, [
    'arguments' => [
        service(LocatorInterface::class),
    ],
]);*/

$builder->setDefinition(LocatorInterface::class, ArrayLocator::class, [
    'pass_tagged' => [
        'command_bus.handler' => function (ArrayLocator $locator, object $service, array $tag) {
            if (isset($tag['handles']) === false) {
                throw new \InvalidArgumentException(sprintf('Missing "handles" option for tagged service "%s".', $service));
            }

            $locator->addHandler($tag['handles'], $service);
        },
    ],
]);
