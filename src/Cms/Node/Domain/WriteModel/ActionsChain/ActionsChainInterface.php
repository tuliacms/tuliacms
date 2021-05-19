<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface ActionsChainInterface
{
    public function execute(string $name, Node $node): void;

    public function addAction(ActionInterface $action, string $name, int $priority): void;
}
