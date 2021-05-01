<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Service;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteRequestExtractor
{
    protected UuidGeneratorInterface $uuidGenerator;
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(UuidGeneratorInterface $uuidGenerator, CurrentWebsiteInterface $currentWebsite)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromRequest(Request $request, array $data = []): array
    {
        return array_merge($data, [
            'id' => $this->uuidGenerator->generate(),
            'backend_prefix' => '/administrator',
            'locales' => [
                [
                    'code' => $request->getPreferredLanguage(),
                    'domain' => $request->getHttpHost(),
                    'domainDevelopment' => $request->getHttpHost(),
                    'sslMode' => SslModeEnum::ALLOWED_BOTH,
                    'isDefault' => true,
                ]
            ]
        ]);
    }
}
