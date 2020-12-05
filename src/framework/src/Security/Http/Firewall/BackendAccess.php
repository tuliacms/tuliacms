<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\Firewall;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Tulia\Component\Routing\Generator\GeneratorInterface;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class BackendAccess
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AccessDecisionManagerInterface
     */
    protected $accessDecisionManager;

    /**
     * @var GeneratorInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $loginPath;

    /**
     * @param TokenStorageInterface          $tokenStorage
     * @param AccessDecisionManagerInterface $accessDecisionManager
     * @param GeneratorInterface             $router
     * @param string                         $loginPath
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AccessDecisionManagerInterface $accessDecisionManager,
        RouterInterface $router,
        string $loginPath
    ) {
        $this->tokenStorage          = $tokenStorage;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->router                 = $router;
        $this->loginPath             = $loginPath;
    }

    /**
     * @param RequestEvent $event
     *
     * @throws RouteNotFoundException
     */
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
