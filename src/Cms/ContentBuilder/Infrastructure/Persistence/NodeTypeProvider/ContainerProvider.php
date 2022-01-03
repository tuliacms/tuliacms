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

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function provide(): array
    {
        $types = [];

        foreach ($this->config as $code => $nodeType) {
            foreach ($nodeType['fields'] as $fieldKey => $field) {
                foreach ($field['constraints'] as $constraintKey => $constraint) {
                    if (isset($constraint['modificators'])) {
                        $modificators = [];

                        foreach ($constraint['modificators'] as $modificator) {
                            $modificators[$modificator['modificator']] = $modificator['value'];
                        }

                        $nodeType['fields'][$fieldKey]['constraints'][$constraintKey]['modificators'] = $modificators;
                    }
                }
            }

            $types[] = $this->buildNodeType($code, $nodeType, true);
        }

        return $types;
    }
}
