<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Event;

use Tulia\Cms\Website\Domain\WriteModel\Aggregate\Locale;
use Tulia\Cms\Website\Domain\WriteModel\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleUpdated extends DomainEvent
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var null|string
     */
    protected $localePrefix;

    /**
     * @var null|string
     */
    protected $pathPrefix;

    /**
     * @var string
     */
    protected $sslMode;

    /**
     * @var bool
     */
    protected $isDefault;

    public function __construct(
        AggregateId $id,
        string $code,
        string $domain,
        ?string $localePrefix,
        ?string $pathPrefix,
        string $sslMode,
        bool $isDefault
    ) {
        parent::__construct($id);

        $this->code = $code;
        $this->domain = $domain;
        $this->localePrefix = $localePrefix;
        $this->pathPrefix = $pathPrefix;
        $this->sslMode = $sslMode;
        $this->isDefault = $isDefault;
    }

    public static function createFromAggregate(AggregateId $id, Locale $locale): self
    {
        return new self(
            $id,
            $locale->getCode(),
            $locale->getDomain(),
            $locale->getLocalePrefix(),
            $locale->getPathPrefix(),
            $locale->getSslMode(),
            $locale->getIsDefault()
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function getSslMode(): string
    {
        return $this->sslMode;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
