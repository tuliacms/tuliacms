<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('filemanager.endpoint', '/filemanager/endpoint', [
        'controller' => 'Tulia\Cms\Filemanager\UI\Web\Controller\Filemanager::endpoint',
        'methods' => [ 'POST' ],
    ]);
});

$collection->add('filemanager.resolve.image.size', '/media/resolve/image/{size}/{id}/{filename}', [
    'controller' => 'Tulia\Cms\Filemanager\UI\Web\Controller\Image::size',
    'defaults' => [
        'size' => 'original',
    ],
]);
