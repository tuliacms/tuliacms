<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Tulia\Cms\Node\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\Event\QueryFilterEvent;

/**
 * @author Adam Banaszkiewicz
 */
class MetadataLoader
{
    /**
     * @var Loader
     */
    protected $loader;

    /**
     * @param Loader $loader
     */
    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param QueryFilterEvent $event
     */
    public function handle(QueryFilterEvent $event): void
    {
        foreach ($event->getCollection() as $node) {
            $this->loader->load($node);
        }
    }
}
