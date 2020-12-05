<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->add('profiler.toolbar', '/_profiler/toolbar/{token}', [
    'controller' => 'Tulia\Cms\Profiler\UI\Web\Controller\Toolbar::index',
]);

$collection->add('profiler.profile', '/_profiler/profile/{token}/{page}', [
    'controller' => 'Tulia\Cms\Profiler\UI\Web\Controller\Profiler::profile',
    'defaults' => [
        'page' => 'request',
    ],
]);
