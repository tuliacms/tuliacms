<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\EventListener\ExceptionListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Exception\RequestCsrfTokenException;

/**
 * @author Adam Banaszkiewicz
 */
class RequestCsrfTokenExceptionListener
{
    private RouterInterface $router;
    private TranslatorInterface $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if (! $event->getThrowable() instanceof RequestCsrfTokenException) {
            return;
        }

        $request = $event->getRequest();

        $this->createFlashMessage($request);

        $event->setResponse($this->generateResponse($request));
    }

    private function createFlashMessage(Request $request): void
    {
        if ($request->hasSession() === false) {
            return;
        }

        $request->getSession()->getFlashBag()->add('warning', $this->translator->trans('csrfTokenNotValidPleaseRedoOperation'));
    }

    private function generateResponse(Request $request): Response
    {
        $referer = $request->headers->get('referer');
        $headers = [
            'X-Tulia-Redirect-Cause' => 'CSRF Token invalid',
        ];

        if ($referer) {
            return new RedirectResponse($referer, Response::HTTP_FOUND, $headers);
        }

        if ($request->attributes->get('_is_backend')) {
            $route = 'backend';
        } else {
            $route = 'homepage';
        }

        return new RedirectResponse($this->router->generate($route), Response::HTTP_FOUND, $headers);
    }
}
