<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('theme', '/theme/list', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Theme::index',
    ]);

    $collection->add('theme.activate', '/theme/activate/{theme}', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Theme::activate',
        'methods' => [ 'POST' ],
    ]);

    $collection->add('theme.customize.current', '/theme/customize', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Customizer::customizeRedirect',
    ]);

    $collection->add('theme.customize.left', '/theme/customize/left/{changeset}', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Customizer::left',
    ]);

    $collection->add('theme.customize.reset', '/theme/customize/reset', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Customizer::reset',
    ]);

    $collection->add('theme.customize.copy_changeset_from_parent', '/theme/customize/copy-changeset-from-parent/{theme}', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Customizer::copyChangesetFromParent',
    ]);

    $collection->add('theme.customize.save', '/theme/customize/save/{theme}/{changeset}', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Customizer::save',
        'methods' => [ 'POST' ],
    ]);

    $collection->add('theme.customize', '/theme/customize/{theme}', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Customizer::customize',
    ]);

    $collection->add('theme.customize', '/theme/customize/{theme}/{changeset}', [
        'controller' => 'Tulia\Cms\Theme\UI\Web\Controller\Backend\Customizer::customize',
    ]);
});
