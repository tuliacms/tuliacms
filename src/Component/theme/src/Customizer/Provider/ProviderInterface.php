<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Provider;

use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\Customizer\CustomizerInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ProviderInterface
{
    public function getPredefined(ChangesetFactoryInterface $factory): array;
    public function build(CustomizerInterface $builder): void;
}
