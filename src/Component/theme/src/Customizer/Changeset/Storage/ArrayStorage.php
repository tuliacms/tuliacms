<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset\Storage;

use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Customizer\Changeset\Changeset;

/**
 * @author Adam Banaszkiewicz
 */
class ArrayStorage implements StorageInterface
{
    protected $changesets = [];

    public function has(string $id): bool
    {
        return isset($this->changesets[$id]);
    }

    public function get(string $id): ChangesetInterface
    {
        return $this->changesets[$id];
    }

    public function remove(ChangesetInterface $changeset)
    {
       unset($this->changesets[$changeset->getId()]);
    }

    public function save(ChangesetInterface $changeset): void
    {
        $this->changesets[$changeset->getId()] = $changeset;
    }

    public function getThemeChangeset(string $theme): ChangesetInterface
    {
        return new Changeset($theme);
    }

    public function setThemeChangeset(string $theme, ChangesetInterface $changeset)
    {

    }

    public function removeThemeChangeset(string $theme, ChangesetInterface $changeset)
    {

    }

    /**
     * @inheritDoc
     */
    public function getActiveChangeset(string $theme): ?ChangesetInterface
    {
        return null;
    }
}
