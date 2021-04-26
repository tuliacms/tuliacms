<?php

namespace Tulia\Cms\Platform\Shared;

use ArrayIterator;
use IteratorAggregate;

class Parameters implements IteratorAggregate
{
    use ParametersTrait;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
}
