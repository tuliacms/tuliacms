<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\LayoutTypeProvider;

use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\AbstractLayoutTypeProviderProvider;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerProvider extends AbstractLayoutTypeProviderProvider
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function provide(): array
    {
        $types = [];

        foreach ($this->config as $name => $options) {
            $types[] = $this->buildLayoutType($name, $options);
        }

        return $types;
    }
}
