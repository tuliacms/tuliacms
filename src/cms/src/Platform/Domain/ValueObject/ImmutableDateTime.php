<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Domain\ValueObject;

use DateTime;
use DateTimeImmutable;

/**
 * @author Adam Banaszkiewicz
 */
final class ImmutableDateTime
{
    /**
     * @var DateTimeImmutable
     */
    private $datetime;

    /**
     * @param string $time
     * @param null $timezone
     *
     * @throws \Exception
     */
    public function __construct($time = 'now', $timezone = null)
    {
        $this->datetime = new DateTimeImmutable($time, $timezone);
    }

    /**
     * @param DateTime $dateTime
     *
     * @return static
     *
     * @throws \Exception
     */
    public static function createFromMutable(DateTime $dateTime): self
    {
        $self =  new self();
        $self->datetime = DateTimeImmutable::createFromMutable($dateTime);

        return $self;
    }

    /**
     * @param ImmutableDateTime $dateTime
     *
     * @return bool
     */
    public function sameAs(self $dateTime): bool
    {
        $that = $this->datetime;
        $new  = $dateTime->datetime;

        return $that->getTimestamp() === $new->getTimestamp()
            && $that->getTimezone()->getName() === $new->getTimezone()->getName();
    }

    public function format(string $format): string
    {
        return $this->datetime->format($format);
    }
}
