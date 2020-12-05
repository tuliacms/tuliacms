<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\EventListener;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tulia\Cms\FrontendToolbar\Application\Builder\Builder;
use Tulia\Framework\Kernel\Event\ResponseEvent;

/**
 * @author Adam Banaszkiewicz
 */
class ToolbarRenderer
{
    /**
     * @var Builder
     */
    private $builder;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param Builder $builder
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(Builder $builder, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->builder = $builder;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function __invoke(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (
            $request->isBackend()
            || $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') === false
        ) {
            return;
        }

        $response = $event->getResponse();
        $content = $response->getContent();

        $toolbar = $this->builder->build($request);

        $content = str_replace('</body>', $toolbar . '</body>', $content);

        $response->setContent($content);
    }
}
