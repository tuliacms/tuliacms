<?php

declare(strict_types=1);

namespace Tulia\Framework\Package;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface as ContainerExtensionInterface;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tulia\Framework\DependencyInjection\ContainerExtension;

/**
 * @author Adam Banaszkiewicz
 */
class FrameworkPackage extends AbstractPackage
{
    public function getContainerExtension(): ?ContainerExtensionInterface
    {
        return new ContainerExtension();
    }

    public function build(ContainerBuilder $builder): void
    {
        $builder->registerForAutoconfiguration(EventSubscriberInterface::class)
            ->addTag('event_subscriber');

        $builder->addCompilerPass(new RegisterListenersPass(
            EventDispatcherInterface::class,
            'event_listener',
            'event_subscriber',
            'event_dispatcher.event_aliases'
        ));
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
