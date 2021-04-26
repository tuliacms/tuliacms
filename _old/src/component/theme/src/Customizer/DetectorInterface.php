<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer;

/**
 * @author Adam Banaszkiewicz
 */
interface DetectorInterface
{
    public function isCustomizerMode(): bool;
    public function getChangesetId(): string;
}
