<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
interface AggregateActionInterface
{
    /**
     * Array of actions this class listen, with priorities.
     * @return array<string, int>
     */
    public static function listen(): array;

    /**
     * Classname which this class want to supports.
     * Supporting class must extends AggregateRoot.
     */
    public static function supports(): string;

    public function execute(AggregateRoot $aggregate): void;
}
