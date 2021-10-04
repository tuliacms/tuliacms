<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\NodeTypeProvider;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\Provider\AbstractNodeTypeProviderProvider;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerProvider extends AbstractNodeTypeProviderProvider
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
            $types[] = $this->buildNodeTypeService($name, $options);
        }

        return $types;
    }
}
