<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Theme\Customizer\Builder\Rendering\Controls\RegistryInterface as ControlsRegistry;
use Tulia\Component\Theme\Customizer\Builder\Structure\Section;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SectionRenderer implements SectionRendererInterface
{
    private TranslatorInterface $translator;
    private ControlsRegistry $controlRegistry;

    public function __construct(
        TranslatorInterface $translator,
        ControlsRegistry $controlRegistry
    ) {
        $this->translator = $translator;
        $this->controlRegistry = $controlRegistry;
    }

    public function render(array $structure, Section $section, ChangesetInterface $changeset): string
    {
        $id = str_replace('.', '_', $section->getCode());
        $parentId = str_replace('.', '_', $section->getParent() ?? 'home');
        $sectionsList = [];

        foreach ($structure as $subsection) {
            if ($subsection->getParent() === $section->getCode()) {
                $sectionId = str_replace('.', '_', $subsection->getCode());
                $sectionsList[] = '<div class="control-trigger" data-show-pane="' . $sectionId . '">'
                    . $this->translator->trans($subsection->getLabel(), [], $subsection->getTransationDomain())
                    . '</div>';
            }
        }

        if ($sectionsList === []) {
            $sections = '';
        } else {
            $sections = '<div class="controls-list">' . implode('', $sectionsList) . '</div>';
        }

        $controls = $this->buildControls($section, $changeset);

        return '<div class="control-pane control-pane-name-' . $id . '" data-section="' . $id . '">
            <div class="control-pane-headline">
                <button type="button" class="control-pane-back" data-show-pane="' . $parentId . '"><i class="icon fas fa-chevron-left"></i></button>
                <h4>' . $this->translator->trans($section->getLabel(), [], $section->getTransationDomain()) . '</h4>
            </div>
            ' . $sections . '
            <div class="control-pane-content">
                ' . $controls . '
            </div>
        </div>';
    }

    private function buildControls(Section $section, ChangesetInterface $changeset): string
    {
        $controls = [];

        foreach ($section->getControls() as $control) {
            $params = $control->toArray();
            $params['translation_domain'] = $section->getTransationDomain();
            $params['value'] = $params['default_value'];

            if ($changeset->has($control->getCode())) {
                $params['value'] = $changeset->get($control->getCode());
            }

            $controls[] = $this->controlRegistry->build($control->getCode(), $control->getType(), $params);
        }

        return implode('', $controls);
    }
}
