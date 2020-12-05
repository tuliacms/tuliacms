<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('me', '/me', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\MyAccount::me',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('me.personalization', '/me/personalization', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\MyAccount::personalization',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('me.edit', '/me/edit', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\MyAccount::edit',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('me.password', '/me/password', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\MyAccount::password',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('user.create', '/user/create', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\User::create',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('user.edit', '/user/edit/{id}', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\User::edit',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('user.delete', '/user/delete', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\User::delete',
        'methods' => [ 'POST' ],
    ]);

    $collection->add('user.search.typeahead', '/user/search/typeahead', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\TypeaheadSearch::handleSearch',
    ]);

    $collection->add('user.datatable', '/user/datatable', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\User::datatable',
    ]);

    $collection->add('user.list', '/user/list', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\User::list',
    ]);

    $collection->add('user', '/user', [
        'controller' => 'Tulia\Cms\User\UI\Web\Controller\User::index',
    ]);
});
