<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Node\Domain\WriteModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface ActionInterface
{
    public static function supports(): array;

    public function execute(Node $node): void;
}
