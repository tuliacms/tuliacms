<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering;

/**
 * @author Adam Banaszkiewicz
 */
class CustomizerView implements \Stringable
{
    private string $content;
    private array $structure;

    public function __construct(
        string $content,
        array $structure
    ) {
        $this->content = $content;
        $this->structure = $structure;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function getStructure(): array
    {
        return $this->structure;
    }
}
