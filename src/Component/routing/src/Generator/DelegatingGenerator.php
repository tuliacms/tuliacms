<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Generator;

use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Component\Routing\Request\RequestContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DelegatingGenerator implements GeneratorInterface
{
    /**
     * @var GeneratorInterface[]
     */
    private $generators = [];

    /**
     * @param iterable $generators
     */
    public function __construct(iterable $generators)
    {
        $this->generators = $generators;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $name, array $params, RequestContextInterface $context): string
    {
        foreach ($this->generators as $generator) {
            try {
                $result = $generator->generate($name, $params, $context);
            } catch (RouteNotFoundException $exception) {
                continue;
            }

            if ($result !== '') {
                return $result;
            }
        }

        throw new RouteNotFoundException(sprintf('Route "%s" not found by any Routing Generators.', $name));
    }
}
