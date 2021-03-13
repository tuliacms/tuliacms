<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\UI\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Installator\Application\Exception\UnknownMigrationVersionException;
use Tulia\Cms\Installator\Application\Service\Steps\AdminAccountInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\AssetsInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\DatabaseInstallator;
use Tulia\Cms\Installator\Application\Service\Steps\WebsiteInstallator;
use Tulia\Cms\Platform\Application\Service\AssetsPublisher;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\NotFoundHttpException;

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

    public function __construct(
        DatabaseInstallator $databaseInstallator,
        AssetsInstallator $assetsInstallator,
        AdminAccountInstallator $adminAccountInstallator,
        WebsiteInstallator $websiteInstallator
    ) {
        $this->databaseInstallator = $databaseInstallator;
        $this->assetsInstallator = $assetsInstallator;
        $this->adminAccountInstallator = $adminAccountInstallator;
        $this->websiteInstallator = $websiteInstallator;
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

        $this->finishStep($request, 'steps.assets');

        return new JsonResponse();
    }

    public function assets(Request $request): JsonResponse
    {
        if ($this->stepFinished($request, 'steps.assets') === false) {
            throw new NotFoundHttpException('Please finish assets step first.');
        }

        $this->assetsInstallator->install();

        $this->finishStep($request, 'steps.admin_account');

        return new JsonResponse();
    }

    public function website(Request $request): JsonResponse
    {
        if ($this->stepFinished($request, 'steps.assets') === false) {
            //throw new NotFoundHttpException('Please finish assets step first.');
        }

        $this->websiteInstallator->install($request->getSession()->get('installator.website'));

        $this->finishStep($request, 'steps.website');

        return new JsonResponse();
    }
}
