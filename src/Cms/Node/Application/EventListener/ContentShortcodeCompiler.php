<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Node\Application\Event\NodeEvent;
use Tulia\Cms\Node\Application\Event\NodePreCreateEvent;
use Tulia\Cms\Node\Application\Event\NodePreUpdateEvent;
use Tulia\Component\Shortcode\ProcessorInterface;

/**
 * Listener is responsible for parsing and compiling Node's source
 * content, and saving this content into `content` field on Node.
 * All operations are done while create or update node at backend.
 *
 * @author Adam Banaszkiewicz
 */
class ContentShortcodeCompiler implements EventSubscriberInterface
{
    protected ProcessorInterface $processor;

    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NodePreCreateEvent::class => ['handle', 0],
            NodePreUpdateEvent::class => ['handle', 0],
        ];
    }

    public function handle(NodeEvent $event): void
    {
        $node = $event->getNode();

        if (! $node->getContentSource()) {
            return;
        }

        $node->setContent(
            $this->processor->process(
                $node->getContentSource()
            )
        );
    }
}
