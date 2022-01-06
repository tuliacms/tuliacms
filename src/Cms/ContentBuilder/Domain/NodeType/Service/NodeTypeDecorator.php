<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeDecorator
{
    /**
     * @var NodeTypeDecoratorInterface[]
     */
    protected array $decorators = [];

    public function addDecorator(NodeTypeDecoratorInterface $decorator): void
    {
        $this->decorators[] = $decorator;
    }

    public function decorate(NodeType $nodeType): void
    {
        foreach ($this->decorators as $decorator) {
            $decorator->decorate($nodeType);
        }
    }
}
