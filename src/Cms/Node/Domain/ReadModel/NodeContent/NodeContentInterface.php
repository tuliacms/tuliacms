<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\NodeContent;

use Stringable;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeContentInterface extends Stringable
{
    public function setSource(?string $source): void;

    public function getSource(): ?string;

    public function getRendered(): string;
}
