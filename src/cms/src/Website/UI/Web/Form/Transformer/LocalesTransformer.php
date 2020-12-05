<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UI\Web\Form\Transformer;

use Tulia\Cms\Website\Query\Model\Locale;
use Tulia\Cms\Website\Website;
use Tulia\Cms\Website\WebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LocalesTransformer
{
    /**
     * @param array $locales
     * @param string|null $defaultLocale
     *
     * @return array
     */
    public static function transformToObjects(WebsiteInterface $website): array
    {
        $result = [];

        foreach ($locales as $locale) {
            if (\is_string($locale)) {
                $l = new Locale();
                $l->setCode($locale);
                $l->setDefault($locale === $website->getDefaultLocale());
                $result[] = $l;
            }
        }

        if (!$defaultLocale && isset($result)) {
            $result[0]->setDefault(true);
        }

        return $result;
    }

    public static function transformToCodes(WebsiteInterface $website): void
    {
        $result = [];

        foreach ($website->getLocales() as $locale) {
            if ($locale instanceof Locale) {
                $result[] = $locale->getCode();
            }
        }

        $website->setLocales($result);

        dump($website);
        exit;
    }
}
