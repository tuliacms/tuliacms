<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Package;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Framework\DependencyInjection\ContainerExtension;
use Tulia\Framework\Package\AbstractPackage;

/**
 * @author Adam Banaszkiewicz
 */
class PlatformPackage extends AbstractPackage
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ContainerExtension();
    }

    public function build(ContainerBuilder $builder): void
    {
        /*$builder->registerForAutoconfiguration(AbstractController::class)
            ->addTag('controller.service_arguments');*/
    }
}