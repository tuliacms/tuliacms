<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class TermActionsChain implements TermActionsChainInterface
{
    protected array $actions = [];

    public function addAction(TermActionInterface $action, string $name, int $priority): void
    {
        $this->actions[$name][$priority][] = $action;
    }

    public function execute(string $name, Term $node): void
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
