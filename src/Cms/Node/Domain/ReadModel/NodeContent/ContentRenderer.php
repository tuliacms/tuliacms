<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\NodeContent;

use Tulia\Cms\Node\Domain\ReadModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class ContentRenderer
{
    private NodeContentFactoryInterface $contentFactory;

    private static array $cache = [];

    public function __construct(NodeContentFactoryInterface $contentFactory)
    {
        $this->contentFactory = $contentFactory;
    }

    public function render(Node $node): void
    {
        foreach ($node->getAttributes() as $name => $value) {
            if (! $value instanceof NodeContentInterface) {
                continue;
            }

            $content = $node[$name . '__compiled'];
            $cacheKey = md5($content);

            if (isset(static::$cache[$cacheKey])) {
                $node->{$name} = static::$cache[$cacheKey];
                return;
            }

            if ($content) {
                $content = $this->contentFactory->createForNode($node, $content);
                $node->{$name} = $content;
                static::$cache[$cacheKey] = $content;
            }
        }
    }
}
