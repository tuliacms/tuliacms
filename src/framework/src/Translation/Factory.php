<?php

declare(strict_types=1);

namespace Tulia\Framework\Translation;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Factory
{
    /**
     * @param CurrentWebsiteInterface $currentWebsite
     * @param array $directories
     * @param bool $debug
     * @param string $cacheDir
     *
     * @return TranslatorInterface
     */
    public static function create(
        CurrentWebsiteInterface $currentWebsite,
        array $directories,
        bool $debug,
        string $cacheDir
    ): TranslatorInterface {
        $translator = new Translator(
            $currentWebsite->getLocale()->getCode(),
            null,
            $cacheDir,
            $debug
        );
        $translator->setFallbackLocales([
            // Language of current locale - pl
            $currentWebsite->getLocale()->getLanguage(),
            // Code of default locale - en_US
            $currentWebsite->getDefaultLocale()->getCode(),
            // Language of default locale - en
            $currentWebsite->getDefaultLocale()->getLanguage(),
            // Always, as the least fallback locale add the `en` language,
            // to make sure, if every website will be showed in EN translations
            // when any of available locales will not have translations.
            'en',
        ]);
        $translator->addLoader('array', new ArrayLoader());
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addLoader('yml', new YamlFileLoader());
        $translator->addLoader('php', new PhpFileLoader());

        // Array unique in case of multiple directory in list.
        $directories = array_unique($directories);

        foreach ($directories as $directory) {
            if (is_dir($directory) === false) {
                continue;
            }

            $files = scandir($directory);

            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                try {
                    [$domain, $code, $extension] = explode('.', $file);
                } catch (\Throwable $e) {
                    throw new \InvalidArgumentException(sprintf('Translation filename must contain domain, locale code and translation separated by dots (example: widgets+intl-icu.en.yml). Given "%s".', $file));
                }

                $translator->addResource($extension, $directory . '/' . $file, $code, $domain);
            }
        }

        return $translator;
    }
}
