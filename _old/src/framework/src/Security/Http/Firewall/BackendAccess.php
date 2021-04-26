<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\Firewall;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class BackendAccess implements EventSubscriberInterface
{
    protected TokenStorageInterface $tokenStorage;
    protected AccessDecisionManagerInterface $accessDecisionManager;
    protected RouterInterface $router;
    protected string $loginPath;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AccessDecisionManagerInterface $accessDecisionManager,
        RouterInterface $router,
        string $loginPath
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->router = $router;
        $this->loginPath = $loginPath;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onRequest', 100],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->isBackend() === false) {
            return;
        }

        $loginPath   = $this->router->generate($this->loginPath, [], RouterInterface::TYPE_URL);
        $currentPath = $request->getSchemeAndHttpHost() . $request->getPathInfo();

        /**
         * If current page is any login page, we allow showing this.
         */
        if (strpos($currentPath, $loginPath) === 0) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            $this->setRedirect($event, $loginPath);
            return;
        }

        $backend  = $this->accessDecisionManager->decide($token, ['BACKEND']);
        $loggedIn = $this->accessDecisionManager->decide($token, ['IS_AUTHENTICATED_FULLY']);

        if (! $backend || ! $loggedIn) {
            $this->setRedirect($event, $loginPath);
        }
    }

    /**
     * @param RequestEvent $event
     * @param string       $loginPath
     */
    private function setRedirect(RequestEvent $event, string $loginPath): void
    {
        $event->setResponse(new RedirectResponse($loginPath, 303));
    }
}
