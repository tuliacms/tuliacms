<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Tulia\Cms\Node\Application\Event\NodeEvent;
use Tulia\Component\Shortcode\ProcessorInterface;

/**
 * Listener is responsible for parsing and compiling Node's source
 * content, and saving this content into `content` field on Node.
 * All operations are done while create or update node at backend.
 *
 * @author Adam Banaszkiewicz
 */
class ContentShortcodeCompiler
{
    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @param ProcessorInterface $processor
     */
    public function __construct(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @param NodeEvent $event
     */
    public function handle(NodeEvent $event): void
    {
        $node = $event->getNode();

        if (!$node->getContentSource()) {
            return;
        }

        $node->setContent(
            $this->processor->process(
                $node->getContentSource()
            )
        );
    }
}
