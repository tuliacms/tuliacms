<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->add('node.get', '/nodes', [
    'controller' => 'Tulia\Cms\Node\UserInterface\API\Controller\Node::list',
]);

$collection->add('node.single.get', '/nodes/{id}', [
    'controller' => 'Tulia\Cms\Node\UserInterface\API\Controller\Node::get',
]);
