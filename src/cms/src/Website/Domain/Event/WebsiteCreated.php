<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\Event;

use Tulia\Cms\Website\Domain\Aggregate\Locale;
use Tulia\Cms\Website\Domain\Aggregate\LocaleCollection;
use Tulia\Cms\Website\Domain\ValueObject\AggregateId;

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

    /**
     * @param AggregateId $id
     * @param string $name
     * @param string $backendPrefix
     * @param LocaleCollection $locales
     */
    public function __construct(AggregateId $id, string $name, string $backendPrefix, LocaleCollection $locales)
    {
        parent::__construct($id);

        $this->name = $name;
        $this->backendPrefix = $backendPrefix;
        $this->locales = $locales;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
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
