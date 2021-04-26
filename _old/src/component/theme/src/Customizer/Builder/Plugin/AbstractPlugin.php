<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Plugin;

use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;
use Tulia\Component\Theme\Customizer\Builder\Section\SectionInterface;
use Tulia\Component\Theme\Customizer\Builder\Controls\ControlInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function compose(BuilderInterface $builder): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function addSection(SectionInterface $section): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function addControl(ControlInterface $control): void
    {

    }
}
