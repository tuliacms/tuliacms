<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Node\Domain\ReadModel\NodeContent\ContentRenderer;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Event\QueryFilterEvent;

/**
 * Listener is responsible for rendering node content at frontend pages.
 * @author Adam Banaszkiewicz
 */
class RegisterContentRenderer implements EventSubscriberInterface
{
    private ContentRenderer $contentRenderer;

    private array $scopes;

    public function __construct(ContentRenderer $contentRenderer, array $scopes = [])
    {
        $this->contentRenderer = $contentRenderer;
        $this->scopes = $scopes;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            QueryFilterEvent::class => ['handle', 0],
        ];
    }

    public function handle(QueryFilterEvent $event): void
    {
        if ($event->getAlias() !== 'node') {
            return;
        }

        if ($event->hasScope($this->scopes) === false) {
            return;
        }

        foreach ($event->getCollection() as $node) {
            $this->contentRenderer->render($node);
        }
    }
}
