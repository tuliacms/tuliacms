<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\UI\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Installator\Application\Exception\UnknownMigrationVersionException;
use Tulia\Cms\Installator\Application\Service\Steps\AdminAccountInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\AssetsInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\DatabaseInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\InstallationFinisher;
use Tulia\Cms\Installator\Application\Service\Steps\WebsiteInstallator;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Adam Banaszkiewicz
 */
class Steps extends AbstractInstallationController
{
    /**
     * @var DatabaseInstallator
     */
    private $databaseInstallator;

    /**
     * @var AssetsInstallator
     */
    private $assetsInstallator;

    /**
     * @var AdminAccountInstallator
     */
    private $adminAccountInstallator;

    /**
     * @var WebsiteInstallator
     */
    private $websiteInstallator;

    /**
     * @var InstallationFinisher
     */
    private $installationFinisher;

    public function __construct(
        DatabaseInstallator $databaseInstallator,
        AssetsInstallator $assetsInstallator,
        AdminAccountInstallator $adminAccountInstallator,
        WebsiteInstallator $websiteInstallator,
        InstallationFinisher $installationFinisher
    ) {
        $this->databaseInstallator = $databaseInstallator;
        $this->assetsInstallator = $assetsInstallator;
        $this->adminAccountInstallator = $adminAccountInstallator;
        $this->websiteInstallator = $websiteInstallator;
        $this->installationFinisher = $installationFinisher;
    }

    public function prepare(Request $request): JsonResponse
    {
        if ($this->stepFinished($request, 'preinstall') === false) {
            throw new NotFoundHttpException('Please finish preinstall step first.');
        }

        try {
            $this->databaseInstallator->install(
                $request->getSession()->get('installator.db')
            );
        } catch (UnknownMigrationVersionException $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->finishStep($request, 'steps.prepare');

        return new JsonResponse();
    }

    public function adminAccount(Request $request): JsonResponse
    {
        if ($this->stepFinished($request, 'steps.prepare') === false) {
            throw new NotFoundHttpException('Please finish prepare step first.');
        }

        $this->adminAccountInstallator->install(
            $request->getSession()->get('installator.user'),
            $request->getSession()->get('installator.website')['code']
        );

        $this->finishStep($request, 'steps.admin_account');

        return new JsonResponse();
    }

    public function website(Request $request): JsonResponse
    {
        if ($this->stepFinished($request, 'steps.admin_account') === false) {
            throw new NotFoundHttpException('Please finish admin_account step first.');
        }

        $this->websiteInstallator->install($request->getSession()->get('installator.website'));

        $this->finishStep($request, 'steps.website');

        return new JsonResponse();
    }

    public function assets(Request $request): JsonResponse
    {
        if ($this->stepFinished($request, 'steps.website') === false) {
            throw new NotFoundHttpException('Please finish website step first.');
        }

        $this->assetsInstallator->install();

        $this->finishStep($request, 'steps.assets');

        return new JsonResponse();
    }

    public function finish(Request $request): JsonResponse
    {
        if ($this->stepFinished($request, 'steps.assets') === false) {
            throw new NotFoundHttpException('Please finish assets step first.');
        }

        $this->installationFinisher->finish();

        $user = $request->getSession()->get('installator.user');
        $website = $request->getSession()->get('installator.website');

        $request->getSession()->set('installator.db', null);
        $request->getSession()->set('installator.user', null);
        $request->getSession()->set('installator.website', null);

        $this->resetSteps($request);

        return new JsonResponse([
            'website' => [
                'panel_url' => $request->getUriForPath($website['backend_prefix'] . '/auth?username=' . $user['username']),
                'frontend' => $request->getUriForPath($website['locale_prefix'] ?? '/'),
            ],
            'user' => [
                'email' => $user['email'],
                'username' => $user['username'],
            ],
        ]);
    }
}
