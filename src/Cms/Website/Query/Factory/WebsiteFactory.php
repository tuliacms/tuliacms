<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Query\Factory;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Website\Application\Service\BackendPrefixGenerator;
use Tulia\Cms\Website\Query\Model\Locale;
use Tulia\Cms\Website\Query\Model\Website;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteFactory implements WebsiteFactoryInterface
{
    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param UuidGeneratorInterface $uuidGenerator
     * @param CurrentWebsiteInterface $currentWebsite
     */
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
