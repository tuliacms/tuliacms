<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Utilities\DateTime;

use DateTime;
use Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class DateFormatter implements DateFormatterInterface
{
    protected string $format = 'Y.m.d';

    /**
     * {@inheritdoc}
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * {@inheritdoc}
     */
    public function format($date, $format = null): string
    {
        $format = $format ?: $this->format;

        if (is_numeric($date)) {
            return date($format, $date);
        }

        if ($date instanceof DateTime || $date instanceof ImmutableDateTime) {
            return $date->format($format);
        } else {
            return (new DateTime($date))->format($format);
        }
    }
}
