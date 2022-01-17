<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\FieldsGroup;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\Section;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeDecorator;

/**
 * @author Adam Banaszkiewicz
 */
class FormDataToModelTransformer
{
    private NodeTypeDecorator $decorator;

    public function __construct(NodeTypeDecorator $decorator)
    {
        $this->decorator = $decorator;
    }

    public function produceNodeType(array $data, LayoutType $layout): NodeType
    {
        $nodeType = new NodeType($data['type']['code'], $layout, false);
        $nodeType->setName($data['type']['name']);
        $nodeType->setIcon($data['type']['icon']);
        $nodeType->setIsHierarchical((bool) $data['type']['icon']);
        $nodeType->setIsRoutable((bool) $data['type']['isRoutable']);
        $nodeType->setRoutableTaxonomyField($data['type']['taxonomyField']);

        foreach ($this->collectFields($data['layout']) as $field) {
            $nodeType->addField($field);
        }

        $this->decorator->decorate($nodeType);

        $nodeType->validate();

        return $nodeType;
    }

    public function produceLayoutType(array $data): LayoutType
    {
        $layoutType = new LayoutType($data['type']['code'] . '_layout');
        $layoutType->setName($data['type']['name'] . ' layout');

        foreach ($this->transformSections($data['layout']) as $section) {
            $layoutType->addSection($section);
        }

        return $layoutType;
    }

    /**
     * @return Field[]
     */
    private function collectFields(array $groups): array
    {
        $fields = [];

        foreach ($groups as $group) {
            foreach ($group['sections'] as $section) {
                foreach ($section['fields'] as $field) {
                    $fields[] = new Field([
                        'code' => $field['code']['value'],
                        'type' => $field['type']['value'],
                        'name' => $field['name']['value'],
                        'is_multilingual' => $field['multilingual']['value'],
                        'configuration' => $this->transformConfiguration($field['configuration']),
                        'constraints' => $this->transformConstraints($field['constraints']),
                    ]);
                }
            }
        }

        return $fields;
    }

    private function transformConfiguration(array $configuration): array
    {
        $result = [];

        foreach ($configuration as $config) {
            $result[$config['id']] = $config['value'];
        }

        return $result;
    }

    private function transformConstraints(array $constraints): array
    {
        $result = [];

        foreach ($constraints as $constraint) {
            if (! $constraint['enabled']) {
                continue;
            }

            $modificators = [];

            foreach ($constraint['modificators'] as $modificator) {
                $modificators[$modificator['id']] = $modificator['value'];
            }

            $result[$constraint['id']]['modificators'] = $modificators;
        }

        return $result;
    }

    private function transformSections(array $sections): array
    {
        $result = [];

        foreach ($sections as $name => $data) {
            $groups = [];

            foreach ($data['sections'] as $group) {
                $fields = [];

                foreach ($group['fields'] as $field) {
                    $fields[] = $field['code']['value'];
                }

                $groups[] = new FieldsGroup(
                    $group['code'],
                    $group['name']['value'],
                    false,
                    'default',
                    $fields
                );
            }

            $result[] = new Section($name, $groups);
        }

        return $result;
    }
}
