<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Shared\Breadcrumbs\Breadcrumbs;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BreadcrumbsGenerator implements BreadcrumbsGeneratorInterface
{
    protected BreadcrumbsResolverRegistryInterface $registry;

    public function __construct(BreadcrumbsResolverRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromRequest(Request $request): BreadcrumbsInterface
    {
        $root = null;

        foreach ($this->registry->all() as $resolver) {
            if ($root = $resolver->findRootCrumb($request)) {
                break;
            }
        }

        if (!$root) {
            return new Breadcrumbs();
        }

        return $this->generateFromIdentity($root);
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromIdentity(Crumb $crumb): BreadcrumbsInterface
    {
        $breadcrumbs = new Breadcrumbs();
        $homepageAdded = $crumb->getCode() === 'homepage';
        $parent = null;
        $securityLooper = 10;

        do {
            $resolverCalled = false;

            foreach ($this->registry->all() as $resolver) {
                if ($resolver->supports($crumb) === false) {
                    continue;
                }

                $parent = $resolver->fillBreadcrumbs($crumb, $breadcrumbs);
                $resolverCalled = true;
            }

            if ($parent === null && $homepageAdded === false) {
                $parent = new Crumb('homepage', []);
                $homepageAdded = true;
            }

            $crumb = $parent;
            $securityLooper--;
        } while ($crumb && $resolverCalled && $securityLooper);

        return $breadcrumbs;
    }
}
