<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\BodyClass\Application\Event\CollectBodyClassEvent;
use Tulia\Cms\BodyClass\Application\EventListener\BodyClass;
use Tulia\Cms\BodyClass\Infrastructure\Framework\Twig\Extension\BodyClassExtension;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(BodyClassExtension::class, BodyClassExtension::class, [
    'arguments' => [
        service(EventDispatcherInterface::class),
        service(RequestStack::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(BodyClass::class, BodyClass::class, [
    'arguments' => [
        service(DetectorInterface::class),
    ],
    'tags' => [
        tag_event_listener(CollectBodyClassEvent::class),
    ],
]);
