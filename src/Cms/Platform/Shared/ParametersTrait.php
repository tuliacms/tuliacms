<?php

namespace Tulia\Cms\Platform\Shared;

use ArrayIterator;

trait ParametersTrait
{
    protected $parameters = [];

    public function get($name, $default = null)
    {
        return $this->parameters[$name] ?? $default;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->parameters);
    }
}
