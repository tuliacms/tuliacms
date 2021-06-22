<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    /**
     * @return NodeTypeInterface[]
     */
    public function all(): iterable;

    /**
     * @param RegistratorInterface $registrator
     */
    public function addRegistrator(RegistratorInterface $registrator): void;

    /**
     * @param string $type
     *
     * @return NodeTypeInterface
     */
    public function registerType(string $type): NodeTypeInterface;

    /**
     * @param string $type
     *
     * @return NodeTypeInterface
     */
    public function getType(string $type): NodeTypeInterface;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isTypeRegistered(string $type): bool;

    /**
     * @return iterable
     */
    public function getRegisteredTypesNames(): iterable;
}
