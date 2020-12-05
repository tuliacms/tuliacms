<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\EventListener;

use Tulia\Cms\User\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\User\Query\Enum\ScopeEnum;
use Tulia\Cms\User\Query\Event\QueryFilterEvent;

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
        foreach ($event->getCollection() as $user) {
            $this->loader->load($user);
        }
    }
}
