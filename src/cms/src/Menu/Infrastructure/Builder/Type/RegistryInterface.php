<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Type;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    public function addRegistrator(RegistratorInterface $registrator): void;

    /**
     * @return TypeInterface[]
     */
    public function all(): array;

    public function registerType(string $type): TypeInterface;
}
