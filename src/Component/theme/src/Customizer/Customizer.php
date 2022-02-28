<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer;

use Tulia\Component\Theme\Customizer\Builder\Structure\StructureRegistry;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\Customizer\Changeset\Transformer\ChangesetFieldsDefinitionControlsTransformer;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Customizer implements CustomizerInterface
{
    private ChangesetFactoryInterface $changesetFactory;
    private StructureRegistry $structureRegistry;
    private array $controls = [];

    public function __construct(
        ChangesetFactoryInterface $changesetFactory,
        StructureRegistry $structureRegistry
    ) {
        $this->changesetFactory = $changesetFactory;
        $this->structureRegistry = $structureRegistry;
    }

    public function configureFieldsTypes(ChangesetInterface $changeset): void
    {
        (new ChangesetFieldsDefinitionControlsTransformer())
            ->transform($changeset, $this->getControls());
    }

    public function getControls(): array
    {
        usort($this->controls, function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        return $this->controls;
    }

    public function fillChangesetWithDefaults(ChangesetInterface $changeset): void
    {
        foreach ($this->structureRegistry->get($changeset->getTheme()) as $section) {
            foreach ($section->getControls() as $control) {
                $changeset->set($control->getCode(), $control->getDefaultValue());
            }
        }
    }

    public function buildDefaultChangeset(ThemeInterface $theme): ChangesetInterface
    {
        $changeset = $this->changesetFactory->factory();
        $changeset->setTheme($theme->getName());

        $this->fillChangesetWithDefaults($changeset);

        return $changeset;
    }

    public function getPredefinedChangesets(): iterable
    {
        return [];
        $predefined = [];

        foreach ($this->providers as $provider) {
            $predefined += $provider->getPredefined($this->changesetFactory);
        }

        return $predefined;
    }
}
