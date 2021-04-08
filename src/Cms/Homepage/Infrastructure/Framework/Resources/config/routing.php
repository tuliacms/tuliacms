<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('system', '/system', [
        'controller' => 'Tulia\Cms\Homepage\UI\Web\Controller\Backend\Misc::system',
    ]);

    $collection->add('tools', '/tools', [
        'controller' => 'Tulia\Cms\Homepage\UI\Web\Controller\Backend\Misc::tools',
    ]);
});

$collection->add('backend', '/', [
    'controller' => 'Tulia\Cms\Homepage\UI\Web\Controller\Backend\Homepage::index',
    'group' => 'backend',
]);

/*$collection->add('homepage', '/', [
    'controller' => 'Tulia\Cms\Homepage\UI\Web\Controller\Frontend\Homepage::index',
]);*/
