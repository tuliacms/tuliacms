<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Breadcrumbs\Application\Crumbs\ResolverInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface as NodeTypeRegistry;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Enum\ScopeEnum;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\NodeFinderInterface;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface as TermFinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CrumbsResolver implements ResolverInterface
{
    protected RouterInterface $router;
    protected NodeTypeRegistry $nodeTypeRegistry;
    protected NodeFinderInterface $nodeFinder;
    protected TermFinderFactoryInterface $termFinderFactory;

    public function __construct(
        RouterInterface $router,
        NodeTypeRegistry $nodeTypeRegistry,
        NodeFinderInterface $nodeFinder,
        TermFinderFactoryInterface $termFinderFactory
    ) {
        $this->router = $router;
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->nodeFinder = $nodeFinder;
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
            $parent = $this->nodeFinder->findOne(['id' => $parentId], ScopeEnum::BREADCRUMBS);

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
