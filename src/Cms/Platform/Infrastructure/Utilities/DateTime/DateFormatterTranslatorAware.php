<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Utilities\DateTime;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Options\Domain\ReadModel\Options;

/**
 * @author Adam Banaszkiewicz
 */
class DateFormatterTranslatorAware extends DateFormatter
{
    private TranslatorInterface $translator;
    private Options $options;
    private string $translationDomain = 'messages';
    private array $translated = [];
    private static array $translations = [
        'January'   => 'january',
        'February'  => 'february',
        'March'     => 'march',
        'April'     => 'april',
        'May'       => 'may',
        'June'      => 'june',
        'July'      => 'july',
        'August'    => 'august',
        'September' => 'september',
        'October'   => 'october',
        'November'  => 'november',
        'December'  => 'december',
        'Monday'    => 'monday',
        'Tuesday'   => 'tuesday',
        'Wednestday'=> 'wednestday',
        'Thursday'  => 'thursday',
        'Friday'    => 'friday',
        'Saturday'  => 'saturday',
        'Sunday'    => 'sunday',
    ];

    public function __construct(TranslatorInterface $translator, Options $options)
    {
        $this->translator = $translator;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function format($date, $format = null): string
    {
        if ($this->translated === []) {
            foreach (static::$translations as $key => $val) {
                $this->translated[$key] = $this->translator->trans($val, [], $this->translationDomain);
            }
        }

        return str_replace(
            array_keys($this->translated),
            array_values($this->translated),
            parent::format($date, $format ?? $this->options->get('date_format', 'j F, Y'))
        );
    }
}
