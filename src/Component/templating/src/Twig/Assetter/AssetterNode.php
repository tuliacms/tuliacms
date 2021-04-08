<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Twig\Assetter;

use Twig\Node\Node;
use Twig\Compiler;

/**
 * @author Adam Banaszkiewicz
 */
class AssetterNode extends Node
{
    /**
     * {@inheritdoc}
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);
        $compiler
            ->raw("\$context['__assetter']->require(\n")
            ->subcompile($this->getNode('values'))
            ->raw(");");
        ;
    }
}
