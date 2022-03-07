<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Implementation\Symfony\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tulia\Component\Importer\FileReader\FileReaderRegistryInterface;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ImporterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->findDefinition(FileReaderRegistryInterface::class);
        $taggedServices = $container->findTaggedServiceIds('importer.file_reader');

        foreach ($taggedServices as $id => $tags) {
            $registry->addMethodCall('addReader', [new Reference($id)]);
        }

        $registry = $container->findDefinition(ObjectImporterRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('importer.object_importer');

        foreach ($taggedServices as $id => $tags) {
            $registry->addMethodCall('addObjectImporter', [new Reference($id)]);
        }
    }
}
