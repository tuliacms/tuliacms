<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer;

use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Customizer\Provider\ProviderInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface CustomizerInterface
{
    public function buildDefaultChangeset(ThemeInterface $theme): ChangesetInterface;

    public function getPredefinedChangesets(): iterable;
}
