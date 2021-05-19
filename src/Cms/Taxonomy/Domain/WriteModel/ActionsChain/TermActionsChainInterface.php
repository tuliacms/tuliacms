<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
interface TermActionsChainInterface
{
    public function execute(string $name, Term $term): void;

    public function addAction(TermActionInterface $action, string $name, int $priority): void;
}
