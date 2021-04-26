<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata;

/**
 * @author Adam Banaszkiewicz
 */
trait MagickMetadataTrait
{
    /**
     * Using fallback to object property is a Twig bugfix. Twig uses __get() method
     * when we want to get something from object if this method is defined.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->{$name} ?? $this->getMeta($name);
    }

    /**
     * @param string $name
     * @param        $value
     *
     * @return mixed
     */
    public function __set(string $name, $value)
    {
        return $this->setMeta($name, $value);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __isset(string $name)
    {
        return $this->hasMeta($name);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $name , array $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        } else {
            return $this->getMeta($name);
        }
    }
}
