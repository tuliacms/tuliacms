<?php

declare(strict_types=1);

namespace Tulia\Framework\Package;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddAnnotationsCachedReaderPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddDebugLogProcessorPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddExpressionLanguageProvidersPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AssetsContextPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ContainerBuilderDebugDumpPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\DataCollectorTranslatorPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\LoggingTranslatorPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ProfilerPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\RemoveUnusedSessionMarshallingHandlerPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\SessionPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerRealRefPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerWeakRefPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\UnusedTagsPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\WorkflowGuardListenerPass;
use Symfony\Component\Cache\DependencyInjection\CacheCollectorPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolClearerPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolPrunerPass;
use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Compiler\RegisterReverseContainerPass;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\Compiler\ValidateEnvPlaceholdersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DependencyInjection\FormPass;
use Symfony\Component\HttpClient\DependencyInjection\HttpClientPass;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\ServiceValueResolver;
use Symfony\Component\HttpKernel\DependencyInjection\ControllerArgumentValueResolverPass;
use Symfony\Component\HttpKernel\DependencyInjection\FragmentRendererPass;
use Symfony\Component\HttpKernel\DependencyInjection\LoggerPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterLocaleAwareServicesPass;
use Symfony\Component\HttpKernel\DependencyInjection\RemoveEmptyControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\DependencyInjection\ResettableServicePass;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mime\DependencyInjection\AddMimeTypeGuesserPass;
use Symfony\Component\PropertyInfo\DependencyInjection\PropertyInfoPass;
use Symfony\Component\Routing\DependencyInjection\RoutingResolverPass;
use Symfony\Component\Serializer\DependencyInjection\SerializerPass;
use Symfony\Component\Translation\DependencyInjection\TranslationDumperPass;
use Symfony\Component\Translation\DependencyInjection\TranslationExtractorPass;
use Symfony\Component\Translation\DependencyInjection\TranslatorPass;
use Symfony\Component\Translation\DependencyInjection\TranslatorPathsPass;
use Symfony\Component\Validator\DependencyInjection\AddAutoMappingConfigurationPass;
use Symfony\Component\Validator\DependencyInjection\AddConstraintValidatorsPass;
use Symfony\Component\Validator\DependencyInjection\AddValidatorInitializersPass;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Tulia\Framework\DependencyInjection\CompilerPass\CommandBusPass;
use Tulia\Framework\DependencyInjection\CompilerPass\TemplatingPass;
use Tulia\Framework\DependencyInjection\CompilerPass\TwigPass;
use Tulia\Framework\DependencyInjection\ContainerExtension;
use Twig\Extension\AbstractExtension;

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
        $builder->addCompilerPass(new RegisterListenersPass(
            EventDispatcherInterface::class,
            'event_listener',
            'kernel.event_subscriber',
            'event_dispatcher.event_aliases'
        ));

        $registerListenersPass = new RegisterListenersPass();
        $registerListenersPass->setHotPathEvents([
            KernelEvents::REQUEST,
            KernelEvents::CONTROLLER,
            KernelEvents::CONTROLLER_ARGUMENTS,
            KernelEvents::RESPONSE,
            KernelEvents::FINISH_REQUEST,
        ]);
        if (class_exists(ConsoleEvents::class)) {
            $registerListenersPass->setNoPreloadEvents([
                ConsoleEvents::COMMAND,
                ConsoleEvents::TERMINATE,
                ConsoleEvents::ERROR,
            ]);
        }

        //$builder->addCompilerPass(new AssetsContextPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
        $builder->addCompilerPass(new LoggerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -32);
        $builder->addCompilerPass(new RegisterControllerArgumentLocatorsPass(ServiceValueResolver::class));
        $builder->addCompilerPass(new RemoveEmptyControllerArgumentLocatorsPass(), PassConfig::TYPE_BEFORE_REMOVING);
        //$builder->addCompilerPass(new RoutingResolverPass());
        //$builder->addCompilerPass(new ProfilerPass());
        // must be registered before removing private services as some might be listeners/subscribers
        // but as late as possible to get resolved parameters
        $builder->addCompilerPass($registerListenersPass, PassConfig::TYPE_BEFORE_REMOVING);
        //$this->addCompilerPassIfExists($builder, AddConstraintValidatorsPass::class);
        //$builder->addCompilerPass(new AddAnnotationsCachedReaderPass(), PassConfig::TYPE_AFTER_REMOVING, -255);
        //$this->addCompilerPassIfExists($builder, AddValidatorInitializersPass::class);
        $this->addCompilerPassIfExists($builder, AddConsoleCommandPass::class, PassConfig::TYPE_BEFORE_REMOVING);
        // must be registered as late as possible to get access to all Twig paths registered in
        // twig.template_iterator definition
        //$this->addCompilerPassIfExists($builder, TranslatorPass::class, PassConfig::TYPE_BEFORE_OPTIMIZATION, -32);
        //$this->addCompilerPassIfExists($builder, TranslatorPathsPass::class, PassConfig::TYPE_AFTER_REMOVING);
        //$builder->addCompilerPass(new LoggingTranslatorPass());
        //$builder->addCompilerPass(new AddExpressionLanguageProvidersPass());
        //$this->addCompilerPassIfExists($builder, TranslationExtractorPass::class);
        //$this->addCompilerPassIfExists($builder, TranslationDumperPass::class);
        //$builder->addCompilerPass(new FragmentRendererPass());
        //$this->addCompilerPassIfExists($builder, SerializerPass::class);
        //$this->addCompilerPassIfExists($builder, PropertyInfoPass::class);
        //$builder->addCompilerPass(new DataCollectorTranslatorPass());
        //$builder->addCompilerPass(new ControllerArgumentValueResolverPass());
        //$builder->addCompilerPass(new CachePoolPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 32);
        //$builder->addCompilerPass(new CachePoolClearerPass(), PassConfig::TYPE_AFTER_REMOVING);
        //$builder->addCompilerPass(new CachePoolPrunerPass(), PassConfig::TYPE_AFTER_REMOVING);
        $this->addCompilerPassIfExists($builder, FormPass::class);
        //$builder->addCompilerPass(new WorkflowGuardListenerPass());
        //$builder->addCompilerPass(new ResettableServicePass());
        $builder->addCompilerPass(new RegisterLocaleAwareServicesPass());
        //$builder->addCompilerPass(new TestServiceContainerWeakRefPass(), PassConfig::TYPE_BEFORE_REMOVING, -32);
        //$builder->addCompilerPass(new TestServiceContainerRealRefPass(), PassConfig::TYPE_AFTER_REMOVING);
        //$this->addCompilerPassIfExists($builder, AddMimeTypeGuesserPass::class);
        //$this->addCompilerPassIfExists($builder, MessengerPass::class);
        //$this->addCompilerPassIfExists($builder, HttpClientPass::class);
        $this->addCompilerPassIfExists($builder, AddAutoMappingConfigurationPass::class);
        $builder->addCompilerPass(new RegisterReverseContainerPass(true));
        $builder->addCompilerPass(new RegisterReverseContainerPass(false), PassConfig::TYPE_AFTER_REMOVING);
        //$builder->addCompilerPass(new RemoveUnusedSessionMarshallingHandlerPass());
        //$builder->addCompilerPass(new SessionPass());

        /*if ($builder->getParameter('kernel.debug')) {
            $builder->addCompilerPass(new AddDebugLogProcessorPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 2);
            $builder->addCompilerPass(new UnusedTagsPass(), PassConfig::TYPE_AFTER_REMOVING);
            $builder->addCompilerPass(new ContainerBuilderDebugDumpPass(), PassConfig::TYPE_BEFORE_REMOVING, -255);
            $builder->addCompilerPass(new CacheCollectorPass(), PassConfig::TYPE_BEFORE_REMOVING);
        }*/

        /*$builder->addCompilerPass(new ValidateEnvPlaceholdersPass());
        //$builder->addCompilerPass(new AddConsoleCommandPass());
        $builder->addCompilerPass(new CommandBusPass());
        $builder->addCompilerPass(new TemplatingPass());
        $builder->addCompilerPass(new TwigPass());
        $builder->addCompilerPass(new RegisterControllerArgumentLocatorsPass(ServiceValueResolver::class));
        $builder->addCompilerPass(new RemoveEmptyControllerArgumentLocatorsPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $builder->addCompilerPass(new ServiceLocatorTagPass());*/

        // @todo Load compiler passes from \Symfony\Bundle\FrameworkBundle\FrameworkBundle::build
    }

    private function addCompilerPassIfExists(ContainerBuilder $container, string $class, string $type = PassConfig::TYPE_BEFORE_OPTIMIZATION, int $priority = 0): void
    {
        $container->addResource(new ClassExistenceResource($class));

        if (class_exists($class)) {
            $container->addCompilerPass(new $class(), $type, $priority);
        }
    }
}
