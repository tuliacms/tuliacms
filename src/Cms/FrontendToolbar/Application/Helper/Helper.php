<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\Helper;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Helper implements HelperInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param EngineInterface $engine
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        EngineInterface $engine
    ) {
        $this->translator = $translator;
        $this->router     = $router;
        $this->engine     = $engine;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RouteNotFoundException
     */
    public function generateUrl(string $route, array $parameters = [], int $referenceType = RouterInterface::TYPE_PATH): string
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

    /**
     * {@inheritdoc}
     */
    public function render(ViewInterface $view): string
    {
        return $this->engine->render($view);
    }
}
