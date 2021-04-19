<?php

declare(strict_types=1);

namespace Tulia\Component\Routing;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsitePrefixesResolver
{
    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->currentWebsite = $currentWebsite;
    }

    public function appendWebsitePrefixes(string $name, string $uri, array $parameters = []): string
    {
        /** @var array $parts */
        $parts = parse_url($uri);

        if (! isset($parts['path'])) {
            $parts['path'] = '/';
        }

        if (isset($parameters['_locale'])) {
            $localePrefix = $this->currentWebsite->getLocaleByCode($parameters['_locale'])->getLocalePrefix();
        } else {
            $localePrefix = $this->currentWebsite->getLocalePrefix();
        }

        if (strncmp($name, 'backend.', 8) === 0) {
            if ($localePrefix !== $this->currentWebsite->getDefaultLocale()->getPathPrefix()) {
                $parts['path'] = str_replace(
                    $this->currentWebsite->getBackendPrefix(),
                    $this->currentWebsite->getBackendPrefix() . $localePrefix,
                    $parts['path']
                );
            }

            $parts['path'] = $this->currentWebsite->getPathPrefix() . $parts['path'];
        } elseif (strncmp($name, 'api.', 4) === 0) {
            $parts['path'] = $this->currentWebsite->getPathPrefix() . $parts['path'];
        } else {
            $parts['path'] = $this->currentWebsite->getPathPrefix() . $localePrefix . $parts['path'];
        }

        return
            (isset($parts['scheme']) ? $parts['scheme'] . '://' : '') .
            ($parts['host'] ?? '') .
            ($parts['path'] ?? '') .
            (isset($parts['query']) ? '?' . $parts['query'] : '')
            ;
    }
}
