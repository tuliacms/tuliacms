<?php

declare(strict_types = 1);

namespace Tulia\Component\Widget\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string $id
     *
     * @return array|null
     */
    public function findById(string $id): ?array;

    /**
     * @param string $space
     *
     * @return array
     */
    public function findBySpace(string $space): array;
}
