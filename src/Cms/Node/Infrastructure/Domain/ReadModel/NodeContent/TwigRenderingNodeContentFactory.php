<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Domain\ReadModel\NodeContent;

use Tulia\Cms\Node\Domain\ReadModel\Model\Node;
use Tulia\Cms\Node\Domain\ReadModel\NodeContent\NodeContentFactoryInterface;
use Tulia\Cms\Node\Domain\ReadModel\NodeContent\NodeContentInterface;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TwigRenderingNodeContentFactory implements NodeContentFactoryInterface
{
    private EngineInterface $engine;

    private string $environment;

    public function __construct(EngineInterface $engine, string $environment)
    {
        $this->engine = $engine;
        $this->environment = $environment;
    }

    public function createForNode(Node $node): NodeContentInterface
    {
        return new TwigRenderingNodeContent($node, $node->getContent()->getSource(), $this->engine, $this->environment);
    }
}
