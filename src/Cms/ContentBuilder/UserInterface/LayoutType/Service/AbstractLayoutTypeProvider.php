<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\Infrastructure\Presentation\TwigLayoutTypeBuilder;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\FieldsGroup;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\LayoutType;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\Section;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractLayoutTypeProvider implements LayoutTypeProviderInterface
{
    protected function buildLayoutType(string $code, array $options): LayoutType
    {
        $layoutType = new LayoutType($code);
        $layoutType->setName($options['name']);
        $layoutType->setBuilder($options['builder'] ?? TwigLayoutTypeBuilder::class);

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
