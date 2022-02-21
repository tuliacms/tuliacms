<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Domain\WriteModel\Event;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleChanged extends DomainEvent
{
    private string $locale;

    public function __construct(string $userId, string $locale)
    {
        parent::__construct($userId);

        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
