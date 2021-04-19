<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RequestMatcher implements EventSubscriberInterface
{
    protected RouterInterface $router;
    protected CurrentWebsiteInterface $currentWebsite;
    protected RouterListener $symfonyListener;

    public function __construct(
        RouterInterface $router,
        CurrentWebsiteInterface $currentWebsite,
        RouterListener $symfonyListener
    ) {
        $this->router = $router;
        $this->currentWebsite = $currentWebsite;
        $this->symfonyListener = $symfonyListener;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 200],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_controller')) {
            return;
        }

        $pathinfo = urldecode($request->attributes->get('_content_path'));
        $fakeRequest = $this->createRequest($pathinfo, $request->attributes->all());
        $fakeEvent = new RequestEvent($event->getKernel(), $fakeRequest, $event->getRequestType());

        $this->symfonyListener->onKernelRequest($fakeEvent);

        $request->attributes->replace($fakeRequest->attributes->all());
    }

    private function createRequest(string $pathinfo, array $attributes): Request
    {
        $context = $this->router->getContext();
        $uri = $pathinfo;
        $serverData = [];

        $host = $context->getHost() ?: 'localhost';

        if ('https' === $context->getScheme() && 443 !== $context->getHttpsPort()) {
            $host .= ':' . $context->getHttpsPort();
        } elseif ('http' === $context->getScheme() && 80 !== $context->getHttpPort()) {
            $host .= ':' . $context->getHttpPort();
        }

        $uri = $context->getScheme() . '://' . $host . $uri . '?' . $context->getQueryString();

        $request = Request::create($uri, $context->getMethod(), $context->getParameters(), [], [], $serverData);
        $request->attributes->add($attributes);

        return $request;
    }
}
