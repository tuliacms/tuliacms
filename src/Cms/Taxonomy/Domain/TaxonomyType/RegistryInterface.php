<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\TaxonomyType;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    /**
     * @return iterable
     */
    public function all(): iterable;

    /**
     * @param RegistratorInterface $registrator
     */
    public function addRegistrator(RegistratorInterface $registrator): void;

    /**
     * @param string $type
     *
     * @return TaxonomyTypeInterface
     */
    public function registerType(string $type): TaxonomyTypeInterface;

    /**
     * @param string $type
     *
     * @return TaxonomyTypeInterface
     */
    public function getType(string $type): TaxonomyTypeInterface;

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
