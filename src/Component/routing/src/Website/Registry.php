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

    /**
     * {@inheritdoc}
     */
    public function add(WebsiteInterface $website): void
    {
        $this->websites[] = $website;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $id): WebsiteInterface
    {
        foreach ($this->websites as $website) {
            if ($website->getId() === $id) {
                return $website;
            }
        }

        throw new WebsiteNotFoundException(sprintf('Website with ID %s not exists in registry.', $id));
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->websites);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->websites[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->websites[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ($offset !== null) {
            $this->websites[$offset] = $value;
        } else {
            $this->websites[] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->websites[$offset]);
    }
}
