<?php

declare(strict_types=1);

namespace Tulia\Framework\Package;

use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\Compiler\ValidateEnvPlaceholdersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tulia\Framework\DependencyInjection\CompilerPass\CommandBusPass;
use Tulia\Framework\DependencyInjection\CompilerPass\TemplatingPass;
use Tulia\Framework\DependencyInjection\CompilerPass\TwigPass;
use Tulia\Framework\DependencyInjection\ContainerExtension;

/**
 * @author Adam Banaszkiewicz
 */
class FrameworkPackage extends AbstractPackage
{
    public function getContainerExtension(): ExtensionInterface
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

        $builder->addCompilerPass(new ValidateEnvPlaceholdersPass());
        $builder->addCompilerPass(new AddConsoleCommandPass());
        $builder->addCompilerPass(new CommandBusPass());
        $builder->addCompilerPass(new TemplatingPass());
        $builder->addCompilerPass(new TwigPass());
    }
}
