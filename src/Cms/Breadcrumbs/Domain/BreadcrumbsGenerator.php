<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Domain;

use Tulia\Cms\Breadcrumbs\Domain\Homepage;
use Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsResolverRegistryInterface;
use Tulia\Cms\Platform\Shared\Breadcrumbs\Breadcrumbs;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Symfony\Component\HttpFoundation\Request;

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

        return $this->generateFromIdentity($root ?? new Homepage());
    }

    /**
     * {@inheritdoc}
     */
    public function generateFromIdentity(object $identity): BreadcrumbsInterface
    {
        $breadcrumbs = new Breadcrumbs();
        $homepageAdded = $identity instanceof Homepage;
        $parent = null;

        do {
            $resolverCalled = false;

            foreach ($this->registry->all() as $resolver) {
                if ($resolver->supports($identity) === false) {
                    continue;
                }

                $parent = $resolver->fillBreadcrumbs($identity, $breadcrumbs);
                $resolverCalled = true;
            }

            if ($parent === null && $homepageAdded === false) {
                $parent = new Homepage();
                $homepageAdded = true;
            }

            $identity = $parent;
        } while ($identity && $resolverCalled);

        return $breadcrumbs;
    }
}
