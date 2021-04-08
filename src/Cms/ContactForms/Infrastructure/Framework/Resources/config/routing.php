<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('form.homepage', '/form', [
        'controller' => 'Tulia\Cms\ContactForms\UI\Web\Controller\Backend\Form::index',
    ]);

    $collection->add('form.list', '/form/list', [
        'controller' => 'Tulia\Cms\ContactForms\UI\Web\Controller\Backend\Form::list',
    ]);

    $collection->add('form.datatable', '/form/datatable', [
        'controller' => 'Tulia\Cms\ContactForms\UI\Web\Controller\Backend\Form::datatable',
    ]);

    $collection->add('form.create', '/form/create', [
        'controller' => 'Tulia\Cms\ContactForms\UI\Web\Controller\Backend\Form::create',
        'methods' => ['POST', 'GET'],
    ]);

    $collection->add('form.edit', '/form/edit/{id}', [
        'controller' => 'Tulia\Cms\ContactForms\UI\Web\Controller\Backend\Form::edit',
        'methods' => ['POST', 'GET'],
    ]);
});

$collection->add('form.submit', '/form/submit/{id}', [
    'controller' => 'Tulia\Cms\ContactForms\UI\Web\Controller\Frontend\Form::submit',
    'methods' => 'POST',
]);
