<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Bridge\Symfony;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteCacheWarmer implements CacheWarmerInterface
{
    private RegistryInterface $websites;
    private CurrentWebsiteInterface $currentWebsite;
    private DebugLoggerInterface $debugLogger;

    public function __construct(
        RegistryInterface $websites,
        CurrentWebsiteInterface $currentWebsite,
        LoggerInterface $debugLogger
    ) {
        $this->websites = $websites;
        $this->currentWebsite = $currentWebsite;
        $this->debugLogger = $debugLogger;
    }

    public function warmUp(string $cacheDir): array
    {
        $this->currentWebsite->set($this->websites->firstActiveWebsite());

        $this->debugLogger->info(
            sprintf('Default website used at Warmup cache is %s.', $this->currentWebsite->getId())
        );

        return [];
    }

    public function isOptional(): bool
    {
        return false;
    }
}
