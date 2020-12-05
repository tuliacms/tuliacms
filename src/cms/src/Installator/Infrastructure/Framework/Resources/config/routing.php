<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('installator', '/', [
        'controller' => 'Tulia\Cms\Installator\UI\Web\Controller\Installator::index',
    ]);

    $collection->add('installator.requirements', '/requirements', [
        'controller' => 'Tulia\Cms\Installator\UI\Web\Controller\Installator::requirements',
    ]);

    $collection->add('installator.database', '/database', [
        'controller' => 'Tulia\Cms\Installator\UI\Web\Controller\Installator::database',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('installator.website', '/website', [
        'controller' => 'Tulia\Cms\Installator\UI\Web\Controller\Installator::website',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('installator.user', '/user', [
        'controller' => 'Tulia\Cms\Installator\UI\Web\Controller\Installator::user',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('installator.install', '/install', [
        'controller' => 'Tulia\Cms\Installator\UI\Web\Controller\Installator::install',
    ]);

    $collection->add('installator.cmd', '/cmd', [
        'controller' => 'Tulia\Cms\Installator\UI\Web\Controller\Installator::cmd',
    ]);

    $collection->add('installator.finish', '/finish', [
        'controller' => 'Tulia\Cms\Installator\UI\Web\Controller\Installator::finish',
    ]);
});
