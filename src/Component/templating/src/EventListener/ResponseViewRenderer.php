<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Tulia\Component\Templating\Engine;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class ResponseViewRenderer implements EventSubscriberInterface
{
    protected Engine $engine;

    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => ['onKernelView', 500],
        ];
    }

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
