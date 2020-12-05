<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Storage;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayStorage implements StorageInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {

    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function rename(string $old, string $new): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $names = []): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function create(string $name, $value, bool $multilingual = false, bool $autoload = null): void
    {

    }
}
