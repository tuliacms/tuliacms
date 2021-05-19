<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy;

/**
 * @author Adam Banaszkiewicz
 */
interface StrategyInterface
{
    public function generate(string $id, string $locale): string;

    public function getName(): string;
}
