<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Controller;

use Psr\Container\ContainerInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\ArgumentNotResolvedException;

/**
 * @author Adam Banaszkiewicz
 */
class ArgumentResolver implements ArgumentResolverInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     * @throws \ReflectionException|ArgumentNotResolvedException
     */
    public function getArguments(Request $request, $class, string $method): array
    {
        if (method_exists($class, $method) === false) {
            return [];
        }

        $reflection = new \ReflectionMethod($class, $method);
        $arguments = [];

        /** @var \ReflectionParameter $param */
        foreach ($reflection->getParameters() as $param) {
            $arguments[] = $this->resolveArgument($request, $param);
        }

        return $arguments;
    }

    /**
     * @param Request $request
     * @param \ReflectionParameter $param
     *
     * @return mixed|null
     *
     * @throws ArgumentNotResolvedException
     */
    private function resolveArgument(Request $request, \ReflectionParameter $param)
    {
        $value = null;

        if ($param->getType() === null) {
            if (($value = $request->get($param->getName())) !== null) {
                return $value;
            }
        } else {
            $type = $param->getType()->getName();

            if ($request instanceof $type) {
                return $request;
            }

            if ($this->container->has($type)) {
                return $this->container->get($type);
            }

            if (($value = $request->get($param->getName())) !== null) {
                return $value;
            }

            if (($value = $request->get($this->transformNameToSnakecase($param->getName()))) !== null) {
                return $value;
            }

            /**
             * @todo Add resolvers collection instead of return null:
             * - Request
             * - Container (services)
             * - Routing (default parameters)
             */
            return null;

            throw new ArgumentNotResolvedException(sprintf('Argument $%s, typed as %s, not resolved as any known service. Maybe You forgot add use statement with fully qualified namespace in Your file?', $param->getName(), $type));
        }
    }

    private function transformNameToCamelcase(string $name): string
    {
        return str_replace(
            ['_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_i', '_j', '_k', '_l', '_m', '_n', '_o', '_p', '_q', '_r', '_s', '_t', '_u', '_v', '_w', '_x', '_y', '_z'],
            ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
            $name
        );
    }

    private function transformNameToSnakecase(string $name): string
    {
        return str_replace(
            ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
            ['_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_i', '_j', '_k', '_l', '_m', '_n', '_o', '_p', '_q', '_r', '_s', '_t', '_u', '_v', '_w', '_x', '_y', '_z'],
            $name
        );
    }
}
