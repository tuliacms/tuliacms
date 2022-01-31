<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Frontend\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Breadcrumbs\Ports\Domain\BreadcrumbsResolverInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CrumbsResolver implements BreadcrumbsResolverInterface
{
    protected RouterInterface $router;

    protected ContentTypeRegistry $contentTypeRegistry;

    protected NodeFinderInterface $nodeFinder;

    protected TermFinderInterface $termFinder;

    public function __construct(
        RouterInterface $router,
        ContentTypeRegistry $contentTypeRegistry,
        NodeFinderInterface $nodeFinder,
        TermFinderInterface $termFinder
    ) {
        $this->router = $router;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->nodeFinder = $nodeFinder;
        $this->termFinder = $termFinder;
    }

    public function findRootCrumb(Request $request): ?object
    {
        $route = $request->attributes->get('_route');
        $node  = $request->attributes->get('node');

        return strncmp($route, 'node.', 5) === 0
            && $this->supports($node)
            ? $node
            : null;
    }

    public function fillBreadcrumbs(object $node, BreadcrumbsInterface $breadcrumbs): ?object
    {
        /** @var Node $node */
        $breadcrumbs->unshift($this->router->generate('node_' . $node->getId()), $node->getTitle());

        if ($this->contentTypeRegistry->has($node->getType())) {
            $type = $this->contentTypeRegistry->get($node->getType());

            if ($type->isHierarchical() && $node->getParentId()) {
                $this->resolveHierarchyCrumbs($node, $breadcrumbs);
                // @todo Implement when node is in taxonomy
            }/* elseif ($type->getRoutableTaxonomyField() && $node->getCategory()) {
                return $this->termFinder->findOne(['id' => $node->getCategory()], TermFinderScopeEnum::BREADCRUMBS);
            }*/
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
            $parent = $this->nodeFinder->findOne(['id' => $parentId], NodeFinderScopeEnum::BREADCRUMBS);

            if ($parent) {
                $nodes[]  = $parent;
                $parentId = $parent->getParentId();
            } else {
                $parentId = null;
            }
        }

        /** @var Node $parent */
        foreach ($nodes as $parent) {
            $breadcrumbs->unshift(
                $this->router->generate(
                    sprintf('node.%s.%s', $parent->getType(), $parent->getId())
                ),
                $parent->getTitle()
            );
        }
    }
}
