<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Service;

use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel\ChildrenTermsLevelStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ChildrenTermsLevelRaiser
{
    private ChildrenTermsLevelStorageInterface $levelStorage;

    private array $levels = [];

    public function __construct(ChildrenTermsLevelStorageInterface $levelStorage)
    {
        $this->levelStorage = $levelStorage;
    }

    public function riseLevelForChildren(string $root, int $baseLevel): void
    {
        $this->levels = [];

        $children = $this->levelStorage->findChildren([$root]);

        if ($children === []) {
            return;
        }

        $this->riseLevelForList($children[$root], $baseLevel);

        $this->levelStorage->updateChildrenLevels($this->levels);

        $this->levels = [];
    }

    private function riseLevelForList(array $list, int $baseLevel): void
    {
        $newLevel = $baseLevel + 1;

        foreach ($list as $id => $currentLevel) {
            if ($currentLevel !== $newLevel) {
                $this->levels[$id] = $newLevel;
            }
        }

        foreach ($this->levelStorage->findChildren(array_keys($list)) as $children) {
            $this->riseLevelForList($children, $newLevel);
        }
    }
}
