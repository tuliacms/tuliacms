<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface extends \ArrayAccess, \IteratorAggregate
{
    public function add(WebsiteInterface $website): void;
}
