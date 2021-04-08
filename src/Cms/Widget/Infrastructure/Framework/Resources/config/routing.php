<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('widget', '/widget', [
        'controller' => 'Tulia\Cms\Widget\UI\Web\Controller\Backend\Widget::index',
    ]);

    $collection->add('widget.list', '/widget/list', [
        'controller' => 'Tulia\Cms\Widget\UI\Web\Controller\Backend\Widget::list',
    ]);

    $collection->add('widget.datatable', '/widget/datatable', [
        'controller' => 'Tulia\Cms\Widget\UI\Web\Controller\Backend\Widget::datatable',
    ]);

    $collection->add('widget.create', '/widget/create/{id}', [
        'controller' => 'Tulia\Cms\Widget\UI\Web\Controller\Backend\Widget::create',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('widget.edit', '/widget/edit/{id}', [
        'controller' => 'Tulia\Cms\Widget\UI\Web\Controller\Backend\Widget::edit',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('widget.delete', '/widget/delete', [
        'controller' => 'Tulia\Cms\Widget\UI\Web\Controller\Backend\Widget::delete',
        'methods' => [ 'POST' ],
    ]);
});
