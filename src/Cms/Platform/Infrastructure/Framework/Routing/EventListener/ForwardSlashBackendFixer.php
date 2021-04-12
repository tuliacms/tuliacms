<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class ForwardSlashBackendFixer
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param RequestEvent $event
     */
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->isBackend() === false) {
            return;
        }

        if (empty($request->getContentPath())) {
            $event->setResponse(new RedirectResponse($this->router->generate('backend', [], RouterInterface::TYPE_URL)));
            $event->stopPropagation();
        }
    }
}
