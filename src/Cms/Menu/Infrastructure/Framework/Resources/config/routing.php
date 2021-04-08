<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('menu', '/menu/list', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\Menu::list'
    ]);

    $collection->add('menu.datatable', '/menu/datatable', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\Menu::datatable',
    ]);

    $collection->add('menu.create', '/menu/create', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\Menu::create',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('menu.edit', '/menu/edit', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\Menu::edit',
        'methods' => [ 'POST' ],
    ]);

    $collection->add('menu.delete', '/menu/delete', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\Menu::delete',
        'methods' => [ 'POST' ],
    ]);

    $collection->add('menu.hierarchy', '/menu/{menuId}/hierarchy', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\Hierarchy::index',
    ]);

    $collection->add('menu.hierarchy.save', '/menu/{menuId}/hierarchy/save', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\Hierarchy::save',
        'methods' => [ 'POST' ],
    ]);



    $collection->add('menu.item', '/menu/{menuId}/item', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\MenuItem::index',
    ]);

    $collection->add('menu.item.list', '/menu/{menuId}/item/list', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\MenuItem::list',
    ]);

    $collection->add('menu.item.datatable', '/menu/{menuId}/item/datatable', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\MenuItem::datatable',
    ]);

    $collection->add('menu.item.create', '/menu/{menuId}/item/create', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\MenuItem::create',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('menu.item.edit', '/menu/{menuId}/item/edit/{id}', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\MenuItem::edit',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('menu.item.delete', '/menu/{menuId}/item/delete', [
        'controller' => 'Tulia\Cms\Menu\UI\Web\Controller\Backend\MenuItem::delete',
        'methods' => [ 'POST' ],
    ]);
});
