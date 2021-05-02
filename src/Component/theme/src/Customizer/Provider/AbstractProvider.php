<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Provider;

use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\Customizer\CustomizerInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractProvider implements ProviderInterface
{
    public function getPredefined(ChangesetFactoryInterface $factory): array
    {
        return  [];
    }

    public function build(CustomizerInterface $builder): void
    {
    }
}
