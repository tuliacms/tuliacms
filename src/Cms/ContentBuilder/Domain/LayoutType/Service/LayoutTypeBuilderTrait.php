<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\LayoutType\Service;

use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\FieldsGroup;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\Model\Section;

/**
 * @author Adam Banaszkiewicz
 */
trait LayoutTypeBuilderTrait
{
    protected function buildLayoutType(string $contentType, array $options): LayoutType
    {
        $layoutType = new LayoutType($options['code']);
        $layoutType->setName($options['name']);
        $layoutType->setBuilder($options['builder'] ?? $this->config->getLayoutBuilder($contentType));

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

        foreach ($groups as $code => $info) {
            $result[$code] = new FieldsGroup(
                $code,
                $info['name'],
                (bool) $info['active'],
                (string) $info['interior'],
                $info['fields']
            );
        }

        return $result;
    }
}
