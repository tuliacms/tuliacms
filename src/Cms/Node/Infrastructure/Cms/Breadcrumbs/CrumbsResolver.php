<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Breadcrumbs\Application\Crumbs\ResolverInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface as NodeTypeRegistry;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\FinderFactoryInterface as NodeFinderFactoryInterface;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface as TermFinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CrumbsResolver implements ResolverInterface
{
    protected RouterInterface $router;
    protected NodeTypeRegistry $nodeTypeRegistry;
    protected NodeFinderFactoryInterface $nodeFinderFactory;
    protected TermFinderFactoryInterface $termFinderFactory;

    public function __construct(
        RouterInterface $router,
        NodeTypeRegistry $nodeTypeRegistry,
        NodeFinderFactoryInterface $nodeFinderFactory,
        TermFinderFactoryInterface $termFinderFactory
    ) {
        $this->router = $router;
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->nodeFinderFactory = $nodeFinderFactory;
        $this->termFinderFactory = $termFinderFactory;
    }

    public function findRootCrumb(Request $request): ?object
    {
        $route = $request->attributes->get('_route');
        $node  = $request->attributes->get('node');

        return strncmp($route, 'node_', 5) === 0
            && $this->supports($node)
            ? $node
            : null;
    }

    public function fillBreadcrumbs(object $node, BreadcrumbsInterface $breadcrumbs): ?object
    {
        /** @var Node $node */

        $breadcrumbs->unshift($this->router->generate('node_' . $node->getId()), $node->getTitle());

        if ($this->nodeTypeRegistry->isTypeRegistered($node->getType())) {
            $type = $this->nodeTypeRegistry->getType($node->getType());

            if ($type->supports('hierarchy') && $node->getParentId()) {
                $this->resolveHierarchyCrumbs($node, $breadcrumbs);
            } elseif ($type->getRoutableTaxonomy() && $node->getCategory()) {
                return $this->termFinderFactory->getInstance(\Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum::BREADCRUMBS)->find($node->getCategory());
            }
        }

        return null;
    }

    public function supports(object $identity): bool
    {
        return $identity instanceof Node;
    }

    private function resolveHierarchyCrumbs(Node $node, BreadcrumbsInterface $breadcrumbs): void
    {
        $parentId = $node->getParentId();
        $nodes = [];

        while ($parentId) {
            $parent = $this->nodeFinderFactory->getInstance(ScopeEnum::BREADCRUMBS)->find($parentId);

            if ($parent) {
                $nodes[]  = $parent;
                $parentId = $parent->getParentId();
            } else {
                $parentId = null;
            }
        }

        foreach ($nodes as $parent) {
            $breadcrumbs->unshift($this->router->generate('node_' . $parent->getId()), $parent->getTitle());
        }
    }
}
