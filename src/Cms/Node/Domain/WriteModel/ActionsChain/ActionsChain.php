<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class ActionsChain implements ActionsChainInterface
{
    protected array $actions = [];

    public function addAction(ActionInterface $action, string $name, int $priority): void
    {
        $this->actions[$name][$priority][] = $action;
    }

    public function execute(string $name, Node $node): void
    {
        if (isset($this->actions[$name]) === false) {
            return;
        }

        krsort($this->actions[$name]);

        foreach ($this->actions[$name] as $actions) {
            foreach ($actions as $action) {
                $action->execute($node);
            }
        }
    }
}
