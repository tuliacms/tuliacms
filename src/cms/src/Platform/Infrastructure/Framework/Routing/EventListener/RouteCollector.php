<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing\EventListener;

use Tulia\Component\Routing\RouteCollectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class RouteCollector
{
    protected RouteCollectionInterface $collection;

    public function __construct(RouteCollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    public function collect(): void
    {
        $collection = $this->collection;

        if (tulia_installed()) {
            include __DIR__ . '/../../Resources/config/routing.php';
        } else {
            include __DIR__ . '/../../../../../Installator/Infrastructure/Framework/Resources/config/routing.php';
        }
    }
}
