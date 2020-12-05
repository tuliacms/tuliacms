<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\EventListener;

/**
 * @author Adam Banaszkiewicz
 */
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Tulia\Framework\Kernel\Event\ViewEvent;
use Tulia\Component\Templating\Engine;
use Tulia\Component\Templating\Exception\ViewNotFoundException;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class ResponseViewRenderer
{
    protected $engine;

    /**
     * ResponseViewRenderer constructor.
     *
     * @param Engine $engine
     */
    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @param ViewEvent $event
     *
     * @throws ViewNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function onKernelView(ViewEvent $event): void
    {
        if (! $event->getControllerResult() instanceof View) {
            return;
        }

        $response = new Response($this->engine->render($event->getControllerResult()));
        $response->headers->set('content-type', 'text/html');

        $event->setResponse($response);
    }
}
