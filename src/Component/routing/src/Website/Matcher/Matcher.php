<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website\Matcher;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Component\Routing\Exception\WebsiteNotFoundException;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;
use Tulia\Component\Routing\Website\Website;
use Tulia\Component\Routing\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Matcher
{
    private static array $staticCache = [];

    /**
     * @throws WebsiteNotFoundException
     */
    public static function matchGlobalsAgainstArray(iterable $websites): array
    {
        return static::matchUrlAgainstArray($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'], $websites);
    }

    /**
     * @throws WebsiteNotFoundException
     */
    public static function matchRequestAgainstArray(Request $request, iterable $websites): array
    {
        return static::matchUrlAgainstArray($request->getHttpHost(), $request->getPathInfo(), $websites);
    }

    /**
     * @throws WebsiteNotFoundException
     */
    public static function matchRequestAgainstObjects(Request $request, iterable $websites): WebsiteInterface
    {
        if (isset(self::$staticCache[$request->getHttpHost() . $request->getPathInfo()])) {
            return self::$staticCache[$request->getHttpHost() . $request->getPathInfo()];
        }

        $websitesArray = [];

        /** @var WebsiteInterface $website */
        foreach ($websites as $website) {
            /** @var LocaleInterface $locale */
            foreach ($website->getLocales() as $locale) {
                $websitesArray[] = [
                    'id' => $website->getId(),
                    'active' => $website->isActive(),
                    'code' => $locale->getCode(),
                    'domain' => $locale->getDomain(),
                    'backend_prefix' => $website->getBackendPrefix(),
                    'path_prefix' => (string) $locale->getPathPrefix(),
                    'locale_prefix' => (string) $locale->getLocalePrefix(),
                ];
            }
        }

        $websiteFound = static::matchUrlAgainstArray($request->getHttpHost(), $request->getPathInfo(), $websitesArray);

        foreach ($websites as $website) {
            if ($website->getId() === $websiteFound['id']) {
                return self::$staticCache[$request->getHttpHost() . $request->getPathInfo()] = Website::withNewLocale($website, $websiteFound['code']);
            }
        }

        throw new WebsiteNotFoundException('No website found with current domain.');
    }

    /**
     * @throws WebsiteNotFoundException
     */
    public static function matchUrlAgainstArray(string $host, string $url, iterable $websites): array
    {
        /**
         * Order by length to prevent situation, when we have two websites with similar path_prefix,
         * like: `/gardens` and `/gardens-vip`, and first one was created first. Matcher needs them
         * in proper order, so first we need to check the longest prefixes.
         */
        usort($websites, function ($a, $b) {
            return \strlen($a['path_prefix']) < \strlen($b['path_prefix']);
        });
        usort($websites, function ($a, $b) {
            return \strlen($a['locale_prefix']) < \strlen($b['locale_prefix']);
        });

        $prepared = [];

        foreach ($websites as $website) {
            $prepared[] = array_merge($website, [
                'prefix' => $website['path_prefix'] . $website['locale_prefix'],
            ]);
            $prepared[] = array_merge($website, [
                'prefix' => $website['path_prefix'] . $website['backend_prefix'] . $website['locale_prefix'],
            ]);
        }

        usort($prepared, function ($a, $b) {
            return \strlen($a['prefix']) < \strlen($b['prefix']);
        });

        $currentWebsite = null;

        /**
         * First search for every website, that contains path prefix.
         * To prevent select website with matched domain, but empty path prefix.
         */
        foreach ($prepared as $website) {
            if (
                $website['domain'] === $host
                && !empty($website['prefix'])
                && strpos($url, $website['prefix']) === 0
            ) {
                $currentWebsite = $website;
                break;
            }
        }

        /**
         * If none of websites with path prefixes were matched,
         * now we want to match websites only by domain.
         */
        if (! $currentWebsite) {
            foreach ($prepared as $website) {
                if (
                    empty($website['prefix'])
                    && $website['domain'] === $host
                ) {
                    $currentWebsite = $website;
                    break;
                }
            }
        }

        if ($currentWebsite === null) {
            throw new WebsiteNotFoundException('No website found with current domain.');
        }

        return $currentWebsite;
    }
}
