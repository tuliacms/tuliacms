<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\FieldsGroup;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\Section;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractLayoutTypeProviderProvider implements LayoutTypeProviderInterface
{
    protected function buildLayoutType(string $name, array $options): LayoutType
    {
        $layoutType = new LayoutType($name, $options['translation_domain']);
        $layoutType->setLabel($options['label']);
        $layoutType->setBuilder($options['builder']);

        foreach ($options['sections'] as $sectionName => $sectionInfo) {
            $layoutType->addSection($this->buildLayoutSection($sectionName, $sectionInfo));
        }

        return $layoutType;
    }

    protected function buildLayoutSection(string $name, array $options): Section
    {
        return new Section($name, $this->buildFieldsGroups($options['groups']));
    }

    private function buildFieldsGroups(array $groups): array
    {
        $result = [];

        foreach ($groups as $name => $info) {
            $result[$name] = new FieldsGroup($name, $info['label'], $info['active'], $info['fields']);
        }

        return $result;
    }
}
