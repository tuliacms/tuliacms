<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Routing\Strategy;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Routing\Strategy\ContentTypeRoutingStrategyInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\Router;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleStrategy implements ContentTypeRoutingStrategyInterface
{
    private NodeFinderInterface $nodeFinder;

    private ContentTypeRegistry $contentTypeRegistry;

    private Router $contentTypeRouter;

    public function __construct(
        NodeFinderInterface $nodeFinder,
        ContentTypeRegistry $contentTypeRegistry,
        Router $contentTypeRouter
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->contentTypeRouter = $contentTypeRouter;
    }

    public function generate(string $id, array $parameters = []): string
    {
        if (isset($parameters['_node_instance'])) {
            return '/' . $parameters['_node_instance']->getSlug();
        }

        $node = $this->findNodeById($id);

        if ($node) {
            return '/' . $node->getSlug();
        }

        return '';
    }

    public function match(string $pathinfo, array $parameters = []): array
    {
        $node = $this->findNodeBySlug(substr($pathinfo, 1));

        if (! $node) {
            return [];
        }

        $nodeType = $this->contentTypeRegistry->get($node->getType());

        if (! $nodeType || $nodeType->isType('node') === false || $nodeType->isRoutable() === false) {
            return [];
        }

        return [
            'node' => $node,
            'slug' => $node->getSlug(),
            '_route' => sprintf('node.%s.%s', $node->getType(), $node->getId()),
            '_controller' => $nodeType->getController(),
        ];
    }

    public function supports(string $contentType): bool
    {
        return $contentType === 'node';
    }

    public function getId(): string
    {
        return 'simple';
    }

    private function findNodeBySlug(?string $slug): ?Node
    {
        return $this->nodeFinder->findOne([
            'slug'      => $slug,
            'per_page'  => 1,
            'order_by'  => null,
            'order_dir' => null,
        ], NodeFinderScopeEnum::ROUTING_MATCHER);
    }

    private function findNodeById(?string $id): ?Node
    {
        return $this->nodeFinder->findOne([
            'id' => $id,
        ], NodeFinderScopeEnum::ROUTING_MATCHER);
    }
}
