<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Activator;

use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Activator implements ActivatorInterface
{
    private StorageInterface $storage;
    private OptionsRepositoryInterface $repository;

    public function __construct(
        StorageInterface $storage,
        OptionsRepositoryInterface $repository
    ) {
        $this->storage = $storage;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function activate(string $name): void
    {
        if ($this->storage->has($name) === false) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        $option = $this->repository->find('theme');
        $option->setValue($name);
        $this->repository->update($option);
    }
}
