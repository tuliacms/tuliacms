<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Activator;

use Tulia\Cms\Options\Application\Service\Options;
use Tulia\Component\Theme\Exception\MissingThemeException;
use Tulia\Component\Theme\Storage\StorageInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Activator implements ActivatorInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var Options
     */
    protected $options;

    public function __construct(StorageInterface $storage, Options $options)
    {
        $this->storage = $storage;
        $this->options = $options;
    }

    /**
     * @param string $name
     *
     * @throws MissingThemeException
     */
    public function activate(string $name): void
    {
        if ($this->storage->has($name) === false) {
            throw new MissingThemeException(sprintf('Theme %s not found in storage.', $name));
        }

        $this->options->set('theme', $name);
    }
}
