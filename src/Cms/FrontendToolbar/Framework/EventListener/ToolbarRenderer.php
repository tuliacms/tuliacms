<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tulia\Cms\FrontendToolbar\Builder\Builder;

/**
 * @author Adam Banaszkiewicz
 */
class ToolbarRenderer implements EventSubscriberInterface
{
    private Builder $builder;
    private AuthorizationCheckerInterface $authorizationChecker;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        Builder $builder,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage
    ) {
        $this->builder = $builder;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => '__invoke',
        ];
    }

    public function __invoke(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (
            $request->attributes->get('_is_backend')
            || strncmp($request->getPathInfo(), '/_wdt', 5) === 0
            || strncmp($request->getPathInfo(), '/_profiler', 10) === 0
        ) {
            return;
        }

        if ($this->tokenStorage->getToken() !== null && $this->authorizationChecker->isGranted('ROLE_ADMIN') === false) {
            return;
        }

        $stylepath = $request->getUriForPath('/assets/core/frontend-toolbar/css/bundle.min.css');
        $scriptpath = $request->getUriForPath('/assets/core/frontend-toolbar/js/bundle.min.js');

        $response = $event->getResponse();
        $content = $response->getContent();

        $toolbar = $this->builder->build($request);
        $toolbar .= '<link rel="stylesheet" type="text/css" href="' . $stylepath . '" />';
        $toolbar .= '<script src="' . $scriptpath . '"></script>';

        $content = str_replace('</body>', $toolbar . '</body>', $content);

        $response->setContent($content);
    }
}
