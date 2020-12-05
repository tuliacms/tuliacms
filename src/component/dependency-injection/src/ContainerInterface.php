<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tulia\Component\DependencyInjection\Exception\MissingParameterException;

/**
 * @author Adam Banaszkiewicz
 */
interface ContainerInterface extends PsrContainerInterface, ParametersInterface
{
    /**
     * @param string $name
     */
    public function mergeGroup(string $name): void;

    /**
     * @param string $name
     *
     * @return Container
     */
    public function getGroup(string $name): Container;

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws MissingParameterException
     *
     * @return mixed Entry.
     */
    public function get($id);

    /**
     * @param string $id
     * @param mixed  $object
     *
     * @return mixed
     */
    public function set(string $id, $object);

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id): bool;

    /**
     * @param string $tag
     *
     * @return array
     */
    public function getTaggedServices(string $tag): array;

    public function lock(): void;

    /**
     * @return bool
     */
    public function isLocked(): bool;
}
