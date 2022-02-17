<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel;

use Tulia\Cms\Shared\Domain\WriteModel\Model\Entity\IndentifyableEntityInterface;

/**
 * @author Adam Banaszkiewicz
 */
class EntitiesChangelog
{
    protected array $entitiesChanges = [];

    public function collectEntitiesChanges(): array
    {
        $changes = $this->entitiesChanges;
        $this->entitiesChanges = [];
        return $changes;
    }

    public function recordEntityChange(string $type, IndentifyableEntityInterface $entity): void
    {
        // Prevents multiple do the same change with the same Entity.
        foreach ($this->entitiesChanges as $key => $change) {
            if ($change['type'] === $type && $change['entity']->getId() === $entity->getId()) {
                unset($this->entitiesChanges[$key]);
            }
        }

        if ($type === 'update') {
            // If Entity has beed added or removed already, we don't add any 'update' changes.
            foreach ($this->entitiesChanges as $change) {
                if ($change['entity']->getId() === $entity->getId() && \in_array($change['type'], ['add', 'remove'])) {
                    return;
                }
            }
        } elseif ($type === 'add' || $type === 'remove') {
            // If Entity has beed added or removed, we remove all the 'update' changes.
            foreach ($this->entitiesChanges as $key => $change) {
                if ($change['entity']->getId() === $entity->getId() && $change['type'] === 'update') {
                    unset($this->entitiesChanges[$key]);
                }
            }
        }

        $this->entitiesChanges[] = ['type' => $type, 'entity' => $entity];
    }
}
