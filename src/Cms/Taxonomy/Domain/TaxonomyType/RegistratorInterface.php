<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\TaxonomyType;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistratorInterface
{
    public function register(RegistryInterface $registry): void;
}
