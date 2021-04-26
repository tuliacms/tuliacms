<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Generator;

use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Component\Routing\Request\RequestContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface GeneratorInterface
{
    /**
     * @param string $name
     * @param array $params
     *
     * @param RequestContextInterface $context
     * @return string
     *
     * @throws RouteNotFoundException
     */
    public function generate(string $name, array $params, RequestContextInterface $context): string;
}
