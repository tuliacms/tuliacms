<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Theme\Activator;

use Tulia\Cms\Options\OptionsInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;
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
     * @var OptionsInterface
     */
    protected $options;

    /**
     * @param StorageInterface $storage
     * @param OptionsInterface $options
     */
    public function __construct(
        StorageInterface $storage,
        OptionsInterface $options
    ) {
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
