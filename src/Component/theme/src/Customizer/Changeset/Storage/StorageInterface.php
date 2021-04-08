<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset\Storage;

use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Exception\ChangesetNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * @param string $id
     *
     * @return ChangesetInterface
     *
     * @throws ChangesetNotFoundException
     */
    public function get(string $id): ChangesetInterface;

    /**
     * @param ChangesetInterface $changeset
     */
    public function save(ChangesetInterface $changeset): void;

    /**
     * @param string $theme
     *
     * @return ChangesetInterface|null
     */
    public function getActiveChangeset(string $theme): ?ChangesetInterface;
}
