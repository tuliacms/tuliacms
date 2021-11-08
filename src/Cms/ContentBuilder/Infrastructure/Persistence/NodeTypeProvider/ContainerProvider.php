<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\NodeTypeProvider;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\AbstractNodeTypeProvider;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerProvider extends AbstractNodeTypeProvider
{
    private array $config;

    public function __construct(FieldTypeMappingRegistry $fieldTypeMappingRegistry, array $config)
    {
        parent::__construct($fieldTypeMappingRegistry);
        $this->config = $config;
    }

    public function provide(): array
    {
        $types = [];

        foreach ($this->config as $name => $options) {
            $types[] = $this->buildNodeType($name, $options);
        }

        return $types;
    }
}
