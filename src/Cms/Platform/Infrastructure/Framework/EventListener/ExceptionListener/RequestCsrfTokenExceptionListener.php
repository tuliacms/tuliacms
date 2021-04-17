<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\EventListener\ExceptionListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Event\ExceptionEvent;
use Tulia\Framework\Security\Http\Csrf\Exception\RequestCsrfTokenException;

/**
 * @author Adam Banaszkiewicz
 */
class RequestCsrfTokenExceptionListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function __invoke(ExceptionEvent $event)
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

        if ($request->isBackend()) {
            $route = 'backend';
        } else {
            $route = 'homepage';
        }

        return new RedirectResponse($this->router->generate($route), Response::HTTP_FOUND, $headers);
    }
}
