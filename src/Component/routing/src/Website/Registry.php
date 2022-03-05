<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Exception\WebsiteNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var WebsiteInterface[]
     */
    protected array $websites = [];

    public function add(WebsiteInterface $website): void
    {
        $this->websites[] = $website;
    }

    public function find(string $id): WebsiteInterface
    {
        foreach ($this->websites as $website) {
            if ($website->getId() === $id) {
                return $website;
            }
        }

        throw new WebsiteNotFoundException(sprintf('Website with ID %s not exists in registry.', $id));
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->websites);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->websites[$offset]);
    }

    /**
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->websites[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset !== null) {
            $this->websites[$offset] = $value;
        } else {
            $this->websites[] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->websites[$offset]);
    }
}
