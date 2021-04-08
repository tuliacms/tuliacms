<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Locale\Locale;

/**
 * @author Adam Banaszkiewicz
 */
class GenericWebsiteRegistryFactory
{
    public static function factory(): RegistryInterface
    {
        $websites = new Registry();

        $localeEnUs = new Locale('en_US', $_SERVER['HTTP_HOST'], null, null, SslModeEnum::ALLOWED_BOTH, true);
        $localePlPl = new Locale('pl_PL', $_SERVER['HTTP_HOST'], null, null, SslModeEnum::ALLOWED_BOTH);

        $websites->add(new Website(
            'f19b16b2-f52b-442a-aee2-8e0f4fed31b7',
            [$localeEnUs, $localePlPl],
            $localeEnUs,
            '/administrator',
            'Default website'
        ));

        return $websites;
    }
}
