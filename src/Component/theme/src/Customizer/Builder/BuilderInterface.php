<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder;

use Tulia\Component\Theme\Customizer\Builder\Rendering\CustomizerView;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    public function build(ChangesetInterface $changeset, ThemeInterface $theme): CustomizerView;
}
