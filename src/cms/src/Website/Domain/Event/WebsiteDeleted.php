<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\Event;

use Tulia\Cms\Website\Domain\Aggregate\Locale;
use Tulia\Cms\Website\Domain\Aggregate\LocaleCollection;
use Tulia\Cms\Website\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteDeleted extends DomainEvent
{
}
