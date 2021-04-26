<?php

declare(strict_types = 1);

namespace Tulia\Component\Widget\Storage;

use Tulia\Component\Widget\WidgetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayStorage implements StorageInterface
{
    protected $storage = [];

    /**
     * {@inheritdoc}
     */
    public function all(?string $space): array
    {
        return $this->storage[$space] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?WidgetInterface
    {
        return null;
    }
}
