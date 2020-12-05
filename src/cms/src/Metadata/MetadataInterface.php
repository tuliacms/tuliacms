<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata;

/**
 * @author Adam Banaszkiewicz
 */
interface MetadataInterface
{
    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set(string $name, $value): void;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param array $metadata
     */
    public function add(array $metadata): void;

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @return array
     */
    public function keys(): array;
}
