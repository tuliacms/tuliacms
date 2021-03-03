<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Utilities\DateTime;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Options\Application\Service\Options;

/**
 * @author Adam Banaszkiewicz
 */
class OptionsFormatterFactory
{
    public static function factory(Options $options, TranslatorInterface $translator): DateFormatterTranslatorAware
    {
        $formatter = new DateFormatterTranslatorAware($translator);
        $formatter->setFormat($options->get('date_format', 'j F, Y'));

        return $formatter;
    }
}