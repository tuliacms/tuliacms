<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering;

use Tulia\Component\Theme\Customizer\Builder\Structure\Section;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface SectionRendererInterface
{
    /**
     * @param Section[] $structure
     */
    public function render(array $structure, Section $section, ChangesetInterface $changeset): string;
}
