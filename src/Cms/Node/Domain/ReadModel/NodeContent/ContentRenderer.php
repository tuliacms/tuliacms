<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\NodeContent;

use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;

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
        $content = $node->getContent();
        $key = md5($content->getSource());

        if (isset(static::$cache[$key])) {
            $node->setContent(static::$cache[$key]);
            return;
        }

        if ($content) {
            $content = $this->contentFactory->createForNode($node);
            $node->setContent($content);
            static::$cache[$key] = $content;
        }
    }
}
