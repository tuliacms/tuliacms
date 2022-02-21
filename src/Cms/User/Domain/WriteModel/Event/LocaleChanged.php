<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

use Tulia\Cms\User\Domain\WriteModel\Model\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleChanged extends DomainEvent
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param AggregateId $userId
     * @param string $locale
     */
    public function __construct(AggregateId $userId, string $locale)
    {
        parent::__construct($userId);

        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
}
