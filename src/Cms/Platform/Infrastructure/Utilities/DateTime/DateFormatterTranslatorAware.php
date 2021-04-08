<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Utilities\DateTime;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DateFormatterTranslatorAware extends DateFormatter
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $translationDomain = 'messages';

    /**
     * @var array
     */
    protected static $translations = [
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

    /**
     * @var array
     */
    protected $translated = [];

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function format($date, $format = null)
    {
        if ($this->translated === []) {
            foreach (static::$translations as $key => $val) {
                $this->translated[$key] = $this->translator->trans($val, [], $this->translationDomain);
            }
        }

        return str_replace(
            array_keys($this->translated),
            array_values($this->translated),
            parent::format($date, $format)
        );
    }
}
