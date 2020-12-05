<?php

declare(strict_types=1);

namespace Tulia\Cms\Options;

use Tulia\Cms\Options\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Options implements OptionsInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorage(StorageInterface $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->storage->get($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->storage->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return $this->storage->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function preload(array $names = []): void
    {
        $this->storage->preload($names);
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $name, $value, bool $multilingual = false, bool $autoload = null): void
    {
        $this->storage->create($name, $value, $multilingual, $autoload);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {
        $this->storage->remove($name);
    }

    /**
     * {@inheritdoc}
     */
    public function rename(string $old, string $new): void
    {
        $this->storage->rename($old, $new);
    }
}
