<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject;

use DateTime;
use DateTimeImmutable;

/**
 * @author Adam Banaszkiewicz
 */
final class ImmutableDateTime extends DateTimeImmutable
{
    private DateTimeImmutable $datetime;

    /**
     * {@inheritdoc}
     */
    public function __construct($time = 'now', $timezone = null)
    {
        parent::__construct($time, $timezone);

        $this->datetime = new DateTimeImmutable($time, $timezone);
    }

    /**
     * @param DateTime $dateTime
     * @return ImmutableDateTime
     * @throws \Exception
     */
    public static function createFromMutable($object)
    {
        $self =  new self();
        $self->datetime = DateTimeImmutable::createFromMutable($object);

        return $self;
    }

    /**
     * @param ImmutableDateTime $dateTime
     * @return bool
     */
    public function sameAs(self $dateTime): bool
    {
        $that = $this->datetime;
        $new  = $dateTime->datetime;

        return $that->getTimestamp() === $new->getTimestamp()
            && $that->getTimezone()->getName() === $new->getTimezone()->getName();
    }

    /**
     * @param string $format
     * @return string
     */
    public function format($format)
    {
        return $this->datetime->format($format);
    }
}
