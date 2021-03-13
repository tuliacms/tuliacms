<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\Application\Service\Steps;

use Tulia\Cms\Platform\Application\Service\AssetsPublisher;

/**
 * @author Adam Banaszkiewicz
 */
class AssetsInstallator
{
    /**
     * @var AssetsPublisher
     */
    private $assetsPublisher;

    public function __construct(AssetsPublisher $assetsPublisher)
    {
        $this->assetsPublisher = $assetsPublisher;
    }

    public function install(): void
    {
        $this->assetsPublisher->publishRegisteredAssets();
    }
}
