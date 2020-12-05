<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('term.search.typeahead', '/term/search/typeahead', [
        'controller' => 'Tulia\Cms\Taxonomy\UI\Web\Controller\Backend\TypeaheadSearch::handleSearch',
    ]);

    $collection->add('term.create', '/term/{taxonomy_type}/create', [
        'controller' => 'Tulia\Cms\Taxonomy\UI\Web\Controller\Backend\Term::create',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('term.edit', '/term/{taxonomy_type}/edit/{id}', [
        'controller' => 'Tulia\Cms\Taxonomy\UI\Web\Controller\Backend\Term::edit',
        'methods' => [ 'GET', 'POST' ],
    ]);

    $collection->add('term.delete', '/term/{taxonomy_type}/delete', [
        'controller' => 'Tulia\Cms\Taxonomy\UI\Web\Controller\Backend\Term::delete',
        'methods' => [ 'POST' ],
    ]);

    $collection->add('term', '/term/{taxonomy_type}/{?list}', [
        'controller' => 'Tulia\Cms\Taxonomy\UI\Web\Controller\Backend\Term::list',
    ]);
});
