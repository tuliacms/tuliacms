<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder;

use Tulia\Component\Theme\Customizer\Builder\Controls\RegistryInterface as ControlsRegistry;
use Tulia\Component\Theme\Customizer\Builder\Plugin\RegistryInterface;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Customizer\Builder\Section\SectionInterface;
use Tulia\Component\Theme\Customizer\CustomizerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    /**
     * @var CustomizerInterface
     */
    protected $customizer;

    /**
     * @var ControlsRegistry
     */
    protected $controlRegistry;

    /**
     * @var RegistryInterface
     */
    protected $plugins;

    /**
     * @var bool
     */
    protected $composed = false;

    /**
     * @param CustomizerInterface $customizer
     * @param ControlsRegistry $controlRegistry
     * @param RegistryInterface $plugins
     */
    public function __construct(
        CustomizerInterface $customizer,
        ControlsRegistry $controlRegistry,
        RegistryInterface $plugins
    ) {
        $this->customizer = $customizer;
        $this->controlRegistry = $controlRegistry;
        $this->plugins = $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ChangesetInterface $changeset): string
    {
        $html = '';

        /** @var SectionInterface $section */
        foreach ($this->customizer->getSections() as $id => $section) {
            $sections = [];

            /** @var SectionInterface $child */
            foreach ($this->customizer->getSections() as $child) {
                if ($child->get('parent') === $section->get('id')) {
                    $sections[] = $child;
                }
            }

            $html .= $section->render($this->buildControls($section, $changeset), $sections);
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function buildControls(SectionInterface $section, ChangesetInterface $changeset): string
    {
        $controls = [];

        foreach ($this->customizer->getControls() as $control) {
            if ($control['section'] !== $section->get('id')) {
                continue;
            }

            if ($changeset->has($control['id'])) {
                $control['value'] = $changeset->get($control['id']);
            }

            $controls[] = $this->controlRegistry->build($control['id'], $control['type'], $control);
        }

        return implode('', $controls);
    }
}
