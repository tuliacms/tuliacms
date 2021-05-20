<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
interface ChildrenTermsLevelStorageInterface
{
    public function findChildren(array $idList): array;

    public function updateChildrenLevels(array $levels): void;
}
