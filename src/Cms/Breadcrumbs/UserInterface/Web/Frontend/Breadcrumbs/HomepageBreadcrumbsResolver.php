<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\UserInterface\Web\Frontend\Breadcrumbs;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Breadcrumbs\Ports\Domain\BreadcrumbsResolverInterface;
use Tulia\Cms\Breadcrumbs\Domain\Homepage;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HomepageBreadcrumbsResolver implements BreadcrumbsResolverInterface
{
    protected TranslatorInterface $translator;
    protected RouterInterface $router;

    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    public function findRootCrumb(Request $request): ?object
    {
        return null;
    }

    public function fillBreadcrumbs(object $identity, BreadcrumbsInterface $breadcrumbs): ?object
    {
        $breadcrumbs->unshift(
            $this->router->generate('homepage'),
            $this->translator->trans('homepage')
        );

        return null;
    }

    public function supports(object $identity): bool
    {
        return $identity instanceof Homepage;
    }
}
