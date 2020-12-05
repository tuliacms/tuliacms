<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel\Controller;

use Psr\Container\ContainerInterface;
use Tulia\Component\DependencyInjection\ContainerAwareInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\ControllerNotCallableException;

/**
 * @author Adam Banaszkiewicz
 */
class ControllerResolver implements ControllerResolverInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ArgumentResolver
     */
    protected $argumentResolver;

    /**
     * @param ContainerInterface $container
     * @param ArgumentResolver   $argumentResolver
     */
    public function __construct(ContainerInterface $container, ArgumentResolver $argumentResolver)
    {
        $this->container        = $container;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * {@inheritdoc}
     * @throws \ReflectionException
     */
    public function getController(Request $request): ?callable
    {
        $controller = $request->attributes->get('_controller');

        if (! $controller) {
            return null;
        }

        $callable = null;

        if (\is_string($controller)) {
            if (strpos($controller, '::') >= 0) {
                [ $classname, $method ] = explode('::', $controller);

                $callable = [ $this->instantiateController($request, $classname), $method ];

                if (\is_callable($callable) === false) {
                    throw new ControllerNotCallableException(sprintf('Controller "%s" is not callable. Missing class or method?', $controller));
                }
            }
        }

        return $callable;
    }

    /**
     * @param string $controller
     *
     * @return mixed
     * @throws \ReflectionException
     */
    private function instantiateController(Request $request, string $controller)
    {
        if ($this->container->has($controller)) {
            return $this->container->get($controller);
        }

        $arguments = $this->argumentResolver->getArguments($request, $controller, '__construct');

        $object = new $controller(...$arguments);

        if ($object instanceof ContainerAwareInterface) {
            $object->setContainer($this->container);
        }

        return $object;
    }
}
