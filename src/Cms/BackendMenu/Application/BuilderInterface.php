<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Application;

use Tulia\Cms\BackendMenu\Application\Registry\ItemRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    /**
     * @param ItemRegistryInterface $registry
     */
    public function build(ItemRegistryInterface $registry): void;
}
