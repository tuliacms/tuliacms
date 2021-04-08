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
    /**
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider): void;

    /**
     * @return iterable
     */
    public function getProviders(): iterable;

    /**
     * @param ThemeInterface $theme
     *
     * @return ChangesetInterface
     */
    public function buildDefaultChangeset(ThemeInterface $theme): ChangesetInterface;

    /**
     * @return iterable
     */
    public function getPredefinedChangesets(): iterable;
}
