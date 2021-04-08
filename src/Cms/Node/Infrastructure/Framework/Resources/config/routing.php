<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollection;
use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    include __DIR__ . '/routing/web.php';
});

$api = new RouteCollection();
$api->group('api', function (RouteCollectionInterface $collection) {
    include __DIR__ . '/routing/api.php';
});
$api->addPathPrefix('/api/v1');

$collection->merge($api);
