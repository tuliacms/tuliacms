<?php declare(strict_types=1);

use Tulia\Component\Routing\RouteCollectionInterface;

/** @var RouteCollectionInterface $collection */

$collection->group('backend', function (RouteCollectionInterface $collection) {
    $collection->add('settings.send_test_email', '/settings/send-test-email', [
        'controller' => 'Tulia\Cms\Settings\UI\Web\Controller\Backend\Settings::sendTestEmail',
        'methods' => [ 'POST' ],
    ]);

    $collection->add('settings', '/settings/{?group}', [
        'controller' => 'Tulia\Cms\Settings\UI\Web\Controller\Backend\Settings::show',
        'methods' => [ 'GET', 'POST' ],
    ]);
});
