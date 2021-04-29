<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel\Finder\Factory;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Website\Application\Service\BackendPrefixGenerator;
use Tulia\Cms\Website\Domain\ReadModel\Finder\Model\Locale;
use Tulia\Cms\Website\Domain\ReadModel\Finder\Model\Website;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteFactory implements WebsiteFactoryInterface
{
    protected UuidGeneratorInterface $uuidGenerator;
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        UuidGeneratorInterface $uuidGenerator,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->uuidGenerator  = $uuidGenerator;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(array $data = []): Website
    {
        return Website::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function createNewFromRequest(Request $request, array $data = []): Website
    {
        $locale = new Locale(
            $request->getPreferredLanguage(),
            $request->getHttpHost(),
            null,
            null,
            SslModeEnum::ALLOWED_BOTH,
            true
        );

        return Website::buildFromArray(array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'locales' => [$locale],
            'backend_prefix' => '/' . BackendPrefixGenerator::generate(),
        ]));
    }
}
