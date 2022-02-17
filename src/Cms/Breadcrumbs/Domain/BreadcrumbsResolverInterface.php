<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface BreadcrumbsResolverInterface
{
    /**
     * Based on Request, resolver finds a root crumb. That means this is a current page
     * crumb, ie. node, term etc. Returning this crumb identity starts
     * breadcrumbs generating process. If resolver
     *
     * @param Request $request
     *
     * @return object|null
     */
    public function findRootCrumb(Request $request): ?Crumb;
    public function fillBreadcrumbs(Crumb $crumb, BreadcrumbsInterface $breadcrumbs): ?Crumb;
    public function supports(Crumb $crumb): bool;
}
