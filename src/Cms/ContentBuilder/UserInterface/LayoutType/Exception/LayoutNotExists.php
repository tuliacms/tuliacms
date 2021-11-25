<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutNotExists extends \Exception
{
    private string $layoutName;
    private string $nodeType;

    public static function fromName(string $name, string $nodeType): self
    {
        $self = new self(sprintf('Layout "%s" not exists for the "%s" node type.', $name, $nodeType));
        $self->layoutName = $name;
        $self->nodeType = $nodeType;

        return $self;
    }

    public function getLayoutName(): string
    {
        return $this->layoutName;
    }

    public function getNodeType(): string
    {
        return $this->nodeType;
    }
}
