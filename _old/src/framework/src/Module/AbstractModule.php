<?php

declare(strict_types=1);

namespace Tulia\Framework\Module;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractModule
{
    protected $name;
    protected $vendor;
    protected $directory;

    public function getName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        $this->resolveName();

        return $this->name;
    }

    public function getVendor(): string
    {
        if ($this->vendor) {
            return $this->vendor;
        }

        $this->resolveName();

        return $this->vendor;
    }

    public function getResourcesDirectory(): string
    {
        return $this->resolveDirectory() . '/Resources';
    }

    protected function resolveDirectory(): string
    {
        if ($this->directory) {
            return $this->directory;
        }

        return $this->directory = realpath(\dirname((new \ReflectionClass($this))->getFileName()));
    }

    protected function resolveName(): void
    {
        [, , $this->vendor, $name] = explode('\\', \get_class($this));
        $this->name = $this->vendor . '/' . $name;

        if (empty($this->name)) {
            throw new \RuntimeException('Cannot resolve $name of the Module. Please provide $name property for Your module.');
        }
    }
}
