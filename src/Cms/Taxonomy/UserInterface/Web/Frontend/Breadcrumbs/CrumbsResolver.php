<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\Breadcrumbs;

use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Breadcrumbs\Application\Crumbs\ResolverInterface;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Enum\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Model\Term;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermFinderInterface;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class CrumbsResolver implements ResolverInterface
{
    protected RouterInterface $router;

    protected RegistryInterface $typeRegistry;

    protected TermFinderInterface $termFinder;

    public function __construct(
        RouterInterface $router,
        RegistryInterface $typeRegistry,
        TermFinderInterface $termFinder
    ) {
        $this->router = $router;
        $this->typeRegistry = $typeRegistry;
        $this->termFinder = $termFinder;
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
            $parent = $this->termFinder->findOne(['id' => $parentId], TermFinderScopeEnum::BREADCRUMBS);

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
