<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('website.create', '/website/create', [
        'controller' => 'Tulia\Cms\Website\UI\Web\Controller\Backend\Website::create',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('website.edit', '/website/edit/{id}', [
        'controller' => 'Tulia\Cms\Website\UI\Web\Controller\Backend\Website::edit',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('website.delete', '/website/delete', [
        'controller' => 'Tulia\Cms\Website\UI\Web\Controller\Backend\Website::delete',
        'methods' => [ 'POST' ],
    ]);

    $collection->add('website', '/website/{?list}', [
        'controller' => 'Tulia\Cms\Website\UI\Web\Controller\Backend\Website::list',
    ]);
});
