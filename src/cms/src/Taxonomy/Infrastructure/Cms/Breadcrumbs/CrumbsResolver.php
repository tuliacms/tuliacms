<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Breadcrumbs;

use Tulia\Cms\Breadcrumbs\Application\Crumbs\ResolverInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class CrumbsResolver implements ResolverInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RegistryInterface
     */
    protected $typeRegistry;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @param RouterInterface $router
     * @param RegistryInterface $typeRegistry
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(
        RouterInterface $router,
        RegistryInterface $typeRegistry,
        FinderFactoryInterface $finderFactory
    ) {
        $this->router = $router;
        $this->typeRegistry = $typeRegistry;
        $this->finderFactory = $finderFactory;
    }

    public function findRootCrumb(Request $request): ?object
    {
        $route = $request->attributes->get('_route');
        $term  = $request->attributes->get('term');

        return strncmp($route, 'term_', 5) === 0
            && $this->supports($term)
            ? $term
            : null;
    }

    public function fillBreadcrumbs(object $term, BreadcrumbsInterface $breadcrumbs): ?object
    {
        /** @var Term $term */

        $breadcrumbs->unshift($this->router->generate('term_' . $term->getId()), $term->getName());

        if ($this->typeRegistry->isTypeRegistered($term->getType())) {
            $type = $this->typeRegistry->getType($term->getType());

            if ($type->supports('hierarchy') && $term->getParentId()) {
                $this->resolveHierarchyCrumbs($breadcrumbs, $term);
            }
        }

        return null;
    }

    public function supports(object $identity): bool
    {
        return $identity instanceof Term;
    }

    private function resolveHierarchyCrumbs(BreadcrumbsInterface $breadcrumbs, Term $term): void
    {
        $parentId = $term->getParentId();
        $terms = [];

        while ($parentId) {
            $parent = $this->finderFactory->getInstance(ScopeEnum::BREADCRUMBS)->find($parentId);

            if ($parent) {
                $terms[] = $parent;
                $parentId = $parent->getParentId();
            } else {
                $parentId = null;
            }
        }

        foreach ($terms as $parent) {
            $breadcrumbs->unshift($this->router->generate('term_' . $parent->getId()), $parent->getName());
        }
    }
}
