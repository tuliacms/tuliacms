<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Exception\WebsiteNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface extends \ArrayAccess, \IteratorAggregate
{
    public function add(WebsiteInterface $website): void;

    /**
     * @param string $id
     * @return WebsiteInterface
     * @throws WebsiteNotFoundException
     */
    public function find(string $id): WebsiteInterface;
}
