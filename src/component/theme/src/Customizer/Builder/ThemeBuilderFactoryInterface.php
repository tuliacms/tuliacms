<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder;

use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ThemeBuilderFactoryInterface
{
    /**
     * @param ThemeInterface $theme
     *
     * @return BuilderInterface
     */
    public function build(ThemeInterface $theme): BuilderInterface;
}
