<?php

declare(strict_types=1);

namespace Tulia\Component\Templating;

use Tulia\Component\Templating\Exception\TwigRenderStringTemplateException;
use Tulia\Component\Templating\Exception\ViewNotFoundException;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @author Adam Banaszkiewicz
 */
class Engine implements EngineInterface
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param ViewInterface $view
     *
     * @return string|null
     *
     * @throws ViewNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(ViewInterface $view): ?string
    {
        $loader       = $this->twig->getLoader();
        $existingView = null;
        $views        = $view->getViews();

        foreach ($views as $viewName) {
            if ($loader->exists($viewName)) {
                $existingView = $viewName;
                break;
            }
        }

        if ($existingView === null) {
            throw ViewNotFoundException::anyViewNotFound($views);
        }

        return $this->twig->render($existingView, $view->getData());
    }

    /**
     * @param string      $view
     * @param array       $data
     * @param string|null $debugName
     *
     * @return string|null
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TwigRenderStringTemplateException
     */
    public function renderString(string $view, array $data = [], string $debugName = null): ?string
    {
        try {
            $template = $this->twig->createTemplate($view, $debugName);
        } catch (Error $e) {
            throw new TwigRenderStringTemplateException(sprintf('Error occured creating string template: %s', $e->getMessage()), $e->getCode(), $e);
        }

        return $this->twig->render($template, $data);
    }
}
