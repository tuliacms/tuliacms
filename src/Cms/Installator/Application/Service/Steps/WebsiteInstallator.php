<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\Application\Service\Steps;

use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Website\Application\Command\WebsiteStorage;
use Tulia\Cms\Website\Application\Model\Locale;
use Tulia\Cms\Website\Application\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteInstallator
{
    /**
     * @var WebsiteStorage
     */
    private $websiteStorage;

    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGenerator;

    public function __construct(WebsiteStorage $websiteStorage, UuidGeneratorInterface $uuidGenerator)
    {
        $this->websiteStorage = $websiteStorage;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function install(array $websiteData): void
    {
        $website = new Website(
            $this->uuidGenerator->generate(),
            $websiteData['name'],
            $websiteData['backend_prefix']
        );
        $website->addLocale(new Locale(
            $websiteData['code'],
            $websiteData['domain'],
            $websiteData['locale_prefix'],
            $websiteData['path_prefix'],
            $websiteData['ssl_mode'],
            true
        ));

        $this->websiteStorage->save($website);
    }
}
