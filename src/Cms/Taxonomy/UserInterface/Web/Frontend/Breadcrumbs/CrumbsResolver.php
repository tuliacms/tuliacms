<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Breadcrumbs\Ports\Domain\BreadcrumbsResolverInterface;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\ReadModel\TaxonomyBreadcrumbs;

/**
 * @author Adam Banaszkiewicz
 */
class CrumbsResolver implements BreadcrumbsResolverInterface
{
    private RouterInterface $router;

    private TaxonomyBreadcrumbs $taxonomyBreadcrumbs;

    public function __construct(
        RouterInterface $router,
        TaxonomyBreadcrumbs $taxonomyBreadcrumbs
    ) {
        $this->router = $router;
        $this->taxonomyBreadcrumbs = $taxonomyBreadcrumbs;
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

    /**
     * @param Term $term
     * @param BreadcrumbsInterface $breadcrumbs
     * @return object|null
     */
    public function fillBreadcrumbs(object $term, BreadcrumbsInterface $breadcrumbs): ?object
    {
        $path = $this->taxonomyBreadcrumbs->find($term->getId());

        foreach ($path as $crumb) {
            if ($crumb->isRoot()) {
                continue;
            }

            $breadcrumbs->unshift($this->router->generate('term_' . $crumb->getId()), $crumb->getName());
        }

        return null;
    }

    public function supports(object $identity): bool
    {
        return $identity instanceof Term;
    }
}
