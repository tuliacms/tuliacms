<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Domain\ReadModel\NodeContent;

use Tulia\Cms\Node\Domain\ReadModel\NodeContent\NodeContentInterface;

/**
 * @author Adam Banaszkiewicz
 */
class VoidNodeContent implements NodeContentInterface
{
    private ?string $source = null;

    public function __construct(?string $source)
    {
        $this->source = $source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function __toString(): string
    {
        return (string) $this->source;
    }

    public function getRendered(): string
    {
        return (string) $this->source;
    }
}
