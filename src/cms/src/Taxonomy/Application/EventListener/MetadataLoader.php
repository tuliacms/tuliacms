<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\EventListener;

use Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\Taxonomy\Query\Event\QueryFilterEvent;

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
        foreach ($event->getCollection() as $term) {
            $this->loader->load($term);
        }
    }
}
