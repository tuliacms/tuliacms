<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('login', '/auth', [
        'controller' => 'Tulia\Cms\Security\UI\Web\Controller\Backend\Security::login',
    ]);
    $collection->add('login.process', '/auth/login', [
        'controller' => 'Tulia\Cms\Security\UI\Web\Controller\Backend\Security::loginProcess',
        'methods' => 'POST',
    ]);
    $collection->add('logout', '/auth/logout', [
        'controller' => 'Tulia\Cms\Security\UI\Web\Controller\Backend\Security::logout',
    ]);
});
