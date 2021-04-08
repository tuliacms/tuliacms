<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Type;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultTypesRegistrator implements RegistratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(RegistryInterface $registry): void
    {
        $type = $registry->registerType('simple:homepage');
        $type->setLabel('itemTypeHomepage');
        $type->setTranslationDomain('menu');

        $type = $registry->registerType('simple:url');
        $type->setLabel('itemTypeUrl');
        $type->setTranslationDomain('menu');
    }
}
