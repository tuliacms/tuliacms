<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Plugin;

use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;
use Tulia\Component\Theme\Customizer\Builder\Controls\ControlInterface;
use Tulia\Component\Theme\Customizer\Builder\Section\SectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface PluginInterface
{
    /**
     * @param BuilderInterface $builder
     */
    public function compose(BuilderInterface $builder): void;

    /**
     * @param SectionInterface $section
     */
    public function addSection(SectionInterface $section): void;

    /**
     * @param ControlInterface $control
     */
    public function addControl(ControlInterface $control): void;
}
