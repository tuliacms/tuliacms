<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder;

use Tulia\Component\Theme\Customizer\Builder\Rendering\CustomizerView;
use Tulia\Component\Theme\Customizer\Builder\Rendering\SectionRendererInterface;
use Tulia\Component\Theme\Customizer\Builder\Structure\StructureRegistry;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    private StructureRegistry $structureRegistry;
    private SectionRendererInterface $sectionRenderer;

    public function __construct(
        StructureRegistry $structureRegistry,
        SectionRendererInterface $sectionRenderer
    ) {
        $this->structureRegistry = $structureRegistry;
        $this->sectionRenderer = $sectionRenderer;
    }

    public function build(ChangesetInterface $changeset, ThemeInterface $theme): CustomizerView
    {
        $structure = $this->structureRegistry->get($theme->getName());
        $result = [];

        foreach ($structure as $section) {
            $result[] = $this->sectionRenderer->render($structure, $section, $changeset);
        }

        return new CustomizerView(implode('', $result), $structure);
    }
}
