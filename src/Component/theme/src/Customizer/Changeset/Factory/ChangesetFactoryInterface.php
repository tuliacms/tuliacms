<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset\Factory;

use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ChangesetFactoryInterface
{
    /**
     * Generated changeset MUST always contain generated unique ID
     * (preferred UUID4).
     *
     * @param string|null $id
     *
     * @return ChangesetInterface
     */
    public function factory(string $id = null): ChangesetInterface;
}
