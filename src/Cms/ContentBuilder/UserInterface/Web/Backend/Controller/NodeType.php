<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeType extends AbstractController
{
    private NodeTypeRegistry $nodeTypeRegistry;
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;

    public function __construct(
        NodeTypeRegistry $nodeTypeRegistry,
        FieldTypeMappingRegistry $fieldTypeMappingRegistry
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    public function create(): ViewInterface
    {
        return $this->view('@backend/content_builder/node_type/create.tpl', [
            'fieldTypes' => $this->getFieldTypes()
        ]);
    }

    private function getFieldTypes(): array
    {
        $types = [];

        foreach ($this->fieldTypeMappingRegistry->all() as $type => $data) {
            $types[$type] = [
                'id' => $type,
                'label' => $data['label'],
                'constraints' => [
                    [
                        'id' => 'required',
                        'label' => 'Required',
                        'help' => 'Makes this field required.',
                    ],
                    [
                        'id' => 'length',
                        'label' => 'Text length',
                        'help' => 'Min and max text length.',
                        'modificators' => [
                            'min' => [
                                'type' => 'integer',
                            ],
                            'max' => [
                                'type' => 'integer',
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $types;
    }
}
