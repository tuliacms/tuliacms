<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('search.search', '/search/search', [
        'controller' => 'Tulia\Cms\SearchAnything\UI\Web\Controller\Backend\Search::search',
    ]);

    $collection->add('search.providers', '/search/providers', [
        'controller' => 'Tulia\Cms\SearchAnything\UI\Web\Controller\Backend\Search::providers',
    ]);

    $collection->add('search.root', '/search', [
        'controller' => 'Tulia\Cms\SearchAnything\UI\Web\Controller\Backend\Search::noop',
    ]);
});
