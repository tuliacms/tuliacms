<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Tulia\Component\DependencyInjection\Exception\DependencyInjectionException;
use Tulia\Component\DependencyInjection\Exception\MissingParameterException;
use Tulia\Component\DependencyInjection\Exception\MissingServiceException;

/**
 * @author Adam Banaszkiewicz
 */
class Container implements ContainerInterface
{
    use ParametersTrait;

    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var Container[]
     */
    protected $groups = [];

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * @param array $services
     * @param array $parameters
     * @param Container[] $groups
     */
    public function __construct(array $services, array $parameters, array $groups = [])
    {
        $this->services   = $services;
        $this->parameters = $parameters;
        $this->groups     = $groups;

        $this->services['parameters_bag']['instance'] = new ParametersBag($this);
        $this->services[PsrContainerInterface::class]['instance'] = $this;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeGroup(string $name): void
    {
        if (isset($this->groups[$name]) === false) {
            return;
        }

        $locked = $this->locked;
        $this->locked = false;
        $this->merge($this->groups[$name]);
        unset($this->groups[$name]);
        $this->locked = $locked;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup(string $name): Container
    {
        return $this->groups[$name];
    }

    protected function getInternal($id, $dependencyOf = null)
    {
        if (! isset($this->services[$id])) {
            if ($dependencyOf) {
                $message = sprintf(
                    'Definition %s not found. Dependency of %s is defined %s.',
                    $id,
                    $dependencyOf,
                    $this->getServiceDebugPlace($dependencyOf)
                );
            } else {
                $message = sprintf('Definition %s not found.', $id);
            }

            throw new MissingServiceException($message);
        }

        if (isset($this->services[$id]['alias_of'])) {
            return $this->getInternal($this->services[$id]['alias_of']);
        }

        $def = $this->services[$id];

        if (isset($def['instance'])) {
            return $def['instance'];
        }

        $arguments = $this->resolveArguments($id, $def['arguments']);

        if ($def['factory']) {
            if ($arguments === []) {
                $object = \call_user_func($def['factory']);
            } else {
                $object = \call_user_func_array($def['factory'], $arguments);
            }
        } else {
            $classname = $def['classname'];

            if ($arguments === []) {
                $object = new $classname();
            } else {
                $object = new $classname(...$arguments);
            }
        }

        /**
         * We have to set the instance before call any next methods to prevent
         * infinite loop, if any of the tagged services use this service.
         */
        $this->services[$id]['instance'] = $object;

        foreach ($def['calls'] as $call) {
            $object->{$call['method']}(...$this->resolveArguments($id, $call['arguments']));
        }

        foreach ($def['pass_tagged'] as $tagName => $method) {
            foreach ($this->getTaggedServices($tagName) as $service => $tags) {
                foreach ($tags as $tag) {
                    if (\is_callable($method)) {
                        $method($object, $this->getInternal($service), $tag);
                    } else {
                        $object->{$method}($this->getInternal($service));
                    }
                }
            }
        }

        foreach ($def['pass_tagged_lazy'] as $tagName => $method) {
            foreach ($this->getTaggedServices($tagName) as $service => $tags) {
                foreach ($tags as $tag) {
                    if (\is_callable($method)) {
                        $method($this, $object, $service, $tag);
                    } else {
                        $object->{$method}($this, $service);
                    }
                }
            }
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->getInternal($id);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $id, $object): void
    {
        if ($this->locked) {
            throw new DependencyInjectionException('Cannot set service when container is locked.');
        }

        if (isset($this->services[$id])) {
            $this->services[$id]['instance'] = $object;
        } else {
            $this->services[$id] = [
                'id' => $id,
                'classname' => \get_class($object),
                'tags' => [],
                'arguments' => [],
                'instance' => $object,
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaggedServices(string $tag): array
    {
        $services = [];

        foreach ($this->services as $id => $service) {
            if (isset($service['tags'][$tag])) {
                $services[$id] = $service['tags'][$tag];
            }
        }

        return $services;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter(string $id, $value): void
    {
        if ($this->locked) {
            throw new DependencyInjectionException('Cannot set parameter when container is locked.');
        }

        $this->parameters[$id] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function lock(): void
    {
        $this->locked = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(ContainerInterface $container): void
    {
        // Remove two default services to prevent parameters fetch bug.
        $services = $container->services;
        unset($services['parameters_bag'], $services[PsrContainerInterface::class]);

        $this->services = array_merge($this->services, $services);

        foreach ($container->getParameters() as $name => $value) {
            if (\is_array($value)) {
                $this->mergeParameter($name, $value);
            } else {
                $this->setParameter($name, $value);
            }
        }
    }

    /**
     * @param $serviceId
     * @param array $arguments
     *
     * @return array
     *
     * @throws MissingParameterException
     * @throws MissingServiceException
     */
    private function resolveArguments($serviceId, array $arguments): array
    {
        $resolved = [];

        foreach ($arguments as $argument) {
            if (\is_string($argument) === false) {
                $resolved[] = $argument;
                continue;
            }

            $value = null;

            if (strncmp($argument, '@', 1) === 0) {
                $value = $this->getInternal(substr($argument, 1), $serviceId);
            } elseif (strncmp($argument, '%?', 2) === 0) {
                try {
                    $value = $this->getParameter(substr($argument, 2));
                } catch (MissingParameterException $e) {
                    $value = null;
                }
            } elseif (strncmp($argument, '%', 1) === 0) {
                $value = $this->getParameter(substr($argument, 1));
            } elseif (strncmp($argument, '!', 1) === 0) {
                $value = $argument;

                if (strncmp($argument, '!tagged:', 8) === 0) {
                    $names = [];
                    $params = [];

                    foreach ($this->getTaggedServices(substr($argument, 8)) as $id => $data) {
                        $names[$id] = $id;
                        $params[$id] = $data;
                    }

                    $value = new LazyServiceIterator($this, $names, $params);
                }
            } else {
                $value = $argument;
            }

            $resolved[] = $value;
        }

        return $resolved;
    }

    private function getServiceDebugPlace($id): ?string
    {
        if (isset($this->services[$id]['debug'])) {
            return sprintf(
                'in %s, on line %d',
                $this->services[$id]['debug']['file'],
                $this->services[$id]['debug']['line']
            );
        }

        return null;
    }
}
