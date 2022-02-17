<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\Breadcrumbs;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsResolverInterface;
use Tulia\Cms\Breadcrumbs\Domain\Crumb;
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

    public function findRootCrumb(Request $request): ?Crumb
    {
        $route = $request->attributes->get('_route');
        $term  = $request->attributes->get('term');

        return strncmp($route, 'term.', 5) === 0
            && $term instanceof Term
            ? new Crumb($route, [ sprintf('term.%s', $term->getId()) ], $term)
            : null;
    }

    public function fillBreadcrumbs(Crumb $crumb, BreadcrumbsInterface $breadcrumbs): ?Crumb
    {
        $path = $this->taxonomyBreadcrumbs->find($crumb->getContext()->getId());

        foreach ($path as $part) {
            if ($part->isRoot()) {
                continue;
            }

            $breadcrumbs->unshift(
                $this->router->generate(
                    sprintf('term.%s.%s', $part->getType(), $part->getId()),
                    [
                        '_term_instance' => $part,
                    ]
                ),
                $part->getTitle()
            );
        }

        return null;
    }

    public function supports(Crumb $crumb): bool
    {
        return $crumb->getContext() instanceof Term;
    }
}
