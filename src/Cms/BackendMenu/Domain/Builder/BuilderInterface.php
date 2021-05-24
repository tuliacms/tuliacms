<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Domain\Builder;

use Tulia\Cms\BackendMenu\Domain\Builder\Registry\ItemRegistryInterface;

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
