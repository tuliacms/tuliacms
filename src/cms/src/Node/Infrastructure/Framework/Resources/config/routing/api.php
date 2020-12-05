<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->add('node.get', '/node', [
    'controller' => 'Tulia\Cms\Node\UI\API\Controller\Node::list',
]);

$collection->add('node.single.get', '/node/{id}', [
    'controller' => 'Tulia\Cms\Node\UI\API\Controller\Node::get',
]);
