<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->add('node.create', '/node/{node_type}/create', [
    'controller' => 'Tulia\Cms\Node\UserInterface\Web\Controller\Backend\Node::create',
    'methods' => [ 'GET', 'POST' ],
]);

$collection->add('node.edit', '/node/{node_type}/edit/{id}', [
    'controller' => 'Tulia\Cms\Node\UserInterface\Web\Controller\Backend\Node::edit',
    'methods' => [ 'GET', 'POST' ],
]);

$collection->add('node.delete', '/node/delete', [
    'controller' => 'Tulia\Cms\Node\UserInterface\Web\Controller\Backend\Node::delete',
    'methods' => [ 'POST' ],
]);

$collection->add('node.change_status', '/node/change-status', [
    'controller' => 'Tulia\Cms\Node\UserInterface\Web\Controller\Backend\Node::changeStatus',
    'methods' => [ 'POST' ],
]);

$collection->add('node.search.typeahead', '/node/search/typeahead', [
    'controller' => 'Tulia\Cms\Node\UserInterface\Web\Controller\Backend\TypeaheadSearch::handleSearch',
]);

$collection->add('node', '/node/{node_type}/{?list}', [
    'controller' => 'Tulia\Cms\Node\UserInterface\Web\Controller\Backend\Node::list',
    'defaults' => [
        'node_type' => 'page',
    ],
]);
