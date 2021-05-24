<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Domain\Builder\Helper;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BuilderHelper implements BuilderHelperInterface
{
    protected TranslatorInterface$translator;
    protected RequestStack $stack;
    protected RouterInterface $router;
    protected string $homepageRoute;

    private ?string $pathinfo = null;
    private ?bool $isHomepage = null;

    public function __construct(
        RequestStack $stack,
        TranslatorInterface $translator,
        RouterInterface $router,
        string $homepageRoute = 'backend.homepage'
    ) {
        $this->stack         = $stack;
        $this->translator    = $translator;
        $this->router        = $router;
        $this->homepageRoute = $homepageRoute;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RouteNotFoundException
     */
    public function isHomepage(): bool
    {
        if ($this->isHomepage) {
            return $this->isHomepage;
        }

        $request = $this->stack->getCurrentRequest();

        if (! $request) {
            return true;
        }

        $homepage = $this->router->generate($this->homepageRoute);

        return $this->isHomepage = ($request->getPathInfo() === $homepage);
    }

    /**
     * {@inheritdoc}
     */
    public function isInPath(string $path): bool
    {
        if ($this->pathinfo === null) {
            $request = $this->stack->getCurrentRequest();

            if (! $request) {
                return false;
            }

            $this->pathinfo = $request->getPathInfo();
        }

        return strpos($this->pathinfo, $path) === 0;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RouteNotFoundException
     */
    public function generateUrl(string $route, array $parameters = [], int $referenceType = RouterInterface::ABSOLUTE_PATH): string
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}
