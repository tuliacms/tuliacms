<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Transformer;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeModelToFormDataTransformer
{
    public function transform(NodeType $nodeType, LayoutType $layoutType): array
    {
        $data = [
            'type' => [
                'code' => [
                    'value' => $nodeType->getCode(),
                ],
                'name' => [
                    'value' => $nodeType->getName(),
                ],
            ],
            'layout' => [
                'sidebar' => [
                    'sections' => $this->transformSections($nodeType, $layoutType, 'sidebar'),
                ],
                'main' => [
                    'sections' => $this->transformSections($nodeType, $layoutType, 'main'),
                ],
            ],
        ];

        dump($nodeType, $layoutType, $data);exit;
    }

    private function transformSections(NodeType $nodeType, LayoutType $layoutType, string $group): array
    {
        $sections = [];

        foreach ($layoutType->getSections() as $section) {
            $sections[] = [

            ];
        }

        return $sections;
    }
}
