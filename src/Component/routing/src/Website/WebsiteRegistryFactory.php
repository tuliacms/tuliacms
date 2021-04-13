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
    public static function getDefaultWebsiteConfiguration(): array
    {
        return [
            [
                'id' => 'f19b16b2-f52b-442a-aee2-8e0f4fed31b7',
                'backend_prefix' => '/administrator',
                'name' => 'Default website',
                'code' => 'en_US',
                'domain' => $_SERVER['HTTP_HOST'] ?? 'tulia.loc',
                'locale_prefix' => null,
                'path_prefix' => '/some-path',
                'ssl_mode' => 'ALLOWED_BOTH',
                'default' => true,
            ],
            [
                'id' => 'f19b16b2-f52b-442a-aee2-8e0f4fed31b7',
                'backend_prefix' => '/administrator',
                'name' => 'Default website',
                'code' => 'pl_PL',
                'domain' => $_SERVER['HTTP_HOST'] ?? 'tulia.loc',
                'locale_prefix' => '/pl',
                'path_prefix' => '/some-path',
                'ssl_mode' => 'ALLOWED_BOTH',
                'default' => false,
            ],
        ];
    }

    public static function factory(array $source): RegistryInterface
    {
        $source = self::groupWebsites($source !== [] ? $source : static::getDefaultWebsiteConfiguration());

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
                    $locale['default']
                );

                if ($locale['default']) {
                    $defaultLocale = current($locales);
                }
            }

            $registry->add(
                new Website(
                    $website['id'],
                    $locales,
                    $defaultLocale,
                    $website['backend_prefix'],
                    $website['name']
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
                    'locales' => [],
                ];
            }

            $result[$website['id']]['locales'][] = [
                'code' => $website['code'],
                'domain' => $website['domain'],
                'locale_prefix' => $website['locale_prefix'],
                'path_prefix' => $website['path_prefix'],
                'ssl_mode' => $website['ssl_mode'],
                'default' => $website['default'],
            ];
        }

        return $result;
    }
}
