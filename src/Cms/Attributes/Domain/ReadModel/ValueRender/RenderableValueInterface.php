<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\ReadModel\ValueRender;

use Stringable;

/**
 * @author Adam Banaszkiewicz
 */
interface RenderableValueInterface extends Stringable
{
    public function setSource(?string $source): void;

    public function getSource(): ?string;

    public function getRendered(): string;
}
