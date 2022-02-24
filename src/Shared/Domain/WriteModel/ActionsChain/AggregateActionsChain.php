<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
class AggregateActionsChain implements AggregateActionsChainInterface
{
    protected array $actions = [];

    public function addAction(AggregateActionInterface $action, string $name, string $supportedClass, int $priority): void
    {
        $this->actions[$name][$supportedClass][$priority][] = $action;
    }

    public function execute(string $name, AggregateRoot $aggregate): void
    {
        $classname = get_class($aggregate);

        if (isset($this->actions[$name][$classname]) === false) {
            return;
        }

        krsort($this->actions[$name][$classname]);

        foreach ($this->actions[$name][$classname] as $actions) {
            /** @var AggregateActionInterface $action */
            foreach ($actions as $action) {
                $action->execute($aggregate);
            }
        }
    }
}
