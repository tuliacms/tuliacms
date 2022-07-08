<?php declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Kernel\TuliaKernel;
use Tulia\Component\Routing\Website\WebsiteProvider;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

/**
 * This is a private constant. In Your code, please use a kernel.project_dir parameter instead.
 */
define('__TULIA_PROJECT_DIR', dirname(__DIR__));

Request::setFactory(function ($query, $request, $attributes, $cookies, $files, $server, $content) {
    $websiteConfigFile = __TULIA_PROJECT_DIR.'/config/dynamic/website.php';
    assert(file_exists($websiteConfigFile), 'Tulia CMS seems to be not installed. Please call make setup do initialize website details.');

    $website = WebsiteProvider::provide(
        $websiteConfigFile,
        $server['HTTP_HOST'],
        $server['REQUEST_URI'],
        $_SERVER['APP_ENV'] === 'dev'
    );

    $attributes['website'] = $website;

    if ($website->isBackend()) {
        $server['REQUEST_URI'] = str_replace($website->getBasepath(), $website->getBackendPrefix(), $server['REQUEST_URI']);
    } else {
        $server['REQUEST_URI'] = str_replace($website->getBasepath(), '', $server['REQUEST_URI']);
    }

    $server['TULIA_WEBSITE_IS_BACKEND'] = $website->isBackend();
    $server['TULIA_WEBSITE_BASEPATH'] = $website->getBasepath();
    $server['TULIA_WEBSITE_BACKEND_PREFIX'] = $website->getBackendPrefix();
    $server['TULIA_WEBSITE_LOCALE'] = $website->getLocale()->getCode();
    $server['TULIA_WEBSITE_LOCALE_DEFAULT'] = $website->getDefaultLocale()->getCode();
    $server['TULIA_WEBSITE_LOCALE_PREFIX'] = $website->getLocale()->getLocalePrefix();
    $server['TULIA_WEBSITE_PATH_PREFIX'] = $website->getLocale()->getPathPrefix();

    return new Request($query, $request, $attributes, $cookies, $files, $server, $content);
});

$request = Request::createFromGlobals();
$kernel = new TuliaKernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG'], $request->attributes->get('website'));
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
