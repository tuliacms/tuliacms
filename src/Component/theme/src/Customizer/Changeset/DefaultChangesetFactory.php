<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset;

use Tulia\Component\Theme\Customizer\Builder\Structure\StructureRegistry;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultChangesetFactory
{
    private ChangesetFactoryInterface $changesetFactory;
    private StructureRegistry $structureRegistry;

    public function __construct(
        ChangesetFactoryInterface $changesetFactory,
        StructureRegistry $structureRegistry
    ) {
        $this->changesetFactory = $changesetFactory;
        $this->structureRegistry = $structureRegistry;
    }

    public function buildForTheme(ThemeInterface $theme): ChangesetInterface
    {
        $changeset = $this->changesetFactory->factory();
        $changeset->setTheme($theme->getName());

        foreach ($this->structureRegistry->get($changeset->getTheme()) as $section) {
            foreach ($section->getControls() as $control) {
                $changeset->set($control->getCode(), $control->getDefaultValue());
            }
        }

        return $changeset;
    }
}
