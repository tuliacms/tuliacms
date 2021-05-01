<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Locale\Locale;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteRegistryFactory
{
    public static function factory(WebsiteProviderInterface $provider): RegistryInterface
    {
        $websites = $provider->provide();

        if ($websites === []) {
            $websites[] = [
                'id' => 'f19b16b2-f52b-442a-aee2-8e0f4fed31b7',
                'backend_prefix' => '/administrator',
                'name' => 'Default website',
                'code' => 'en_US',
                'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                'locale_prefix' => null,
                'path_prefix' => null,
                'ssl_mode' => SslModeEnum::ALLOWED_BOTH,
                'is_default' => true,
            ];
        }

        $source = self::groupWebsites($websites);

        $registry = new Registry();

        foreach ($source as $website) {
            $locales = [];
            $defaultLocale = null;

            foreach ($website['locales'] as $locale) {
                $locales[] = new Locale(
                    $locale['code'],
                    $locale['domain'],
                    $locale['locale_prefix'],
                    $locale['path_prefix'],
                    $locale['ssl_mode'],
                    (bool) $locale['is_default']
                );

                if ($locale['is_default']) {
                    $defaultLocale = current($locales);
                }
            }

            $registry->add(
                new Website(
                    $website['id'],
                    $locales,
                    $defaultLocale,
                    $website['backend_prefix'],
                    $website['name'],
                    $website['active']
                )
            );
        }

        return $registry;
    }

    private static function groupWebsites(array $websites): array
    {
        $result = [];

        foreach ($websites as $website) {
            if (! isset($result[$website['id']])) {
                $result[$website['id']] = [
                    'id' => $website['id'],
                    'backend_prefix' => $website['backend_prefix'],
                    'name' => $website['name'],
                    'active' => (bool) $website['active'],
                    'locales' => [],
                ];
            }

            $result[$website['id']]['locales'][] = [
                'code' => $website['code'],
                'domain' => $website['domain'],
                'locale_prefix' => $website['locale_prefix'],
                'path_prefix' => $website['path_prefix'],
                'ssl_mode' => $website['ssl_mode'],
                'is_default' => (bool) $website['is_default'],
            ];
        }

        return $result;
    }
}
