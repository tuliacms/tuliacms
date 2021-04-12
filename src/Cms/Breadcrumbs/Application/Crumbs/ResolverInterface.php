<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Application\Crumbs;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ResolverInterface
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
    public function findRootCrumb(Request $request): ?object;
    public function fillBreadcrumbs(object $identity, BreadcrumbsInterface $breadcrumbs): ?object;
    public function supports(object $identity): bool;
}
