<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Event;

use Tulia\Cms\Website\Domain\WriteModel\Aggregate\Locale;
use Tulia\Cms\Website\Domain\WriteModel\Aggregate\LocaleCollection;
use Tulia\Cms\Website\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteCreated extends DomainEvent
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $backendPrefix;

    /**
     * @var LocaleCollection|Locale[]|array
     */
    private $locales;

    public function __construct(AggregateId $id, string $name, string $backendPrefix, LocaleCollection $locales)
    {
        parent::__construct($id);

        $this->name = $name;
        $this->backendPrefix = $backendPrefix;
        $this->locales = $locales;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBackendPrefix(): string
    {
        return $this->backendPrefix;
    }

    /**
     * @return array|Locale[]|LocaleCollection
     */
    public function getLocales()
    {
        return $this->locales;
    }
}
