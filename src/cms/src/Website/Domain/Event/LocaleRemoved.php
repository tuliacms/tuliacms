<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\Event;

use Tulia\Cms\Website\Domain\Aggregate\Locale;
use Tulia\Cms\Website\Domain\ValueObject\AggregateId;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleRemoved extends DomainEvent
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

    /**
     * @param AggregateId $id
     * @param string $code
     * @param string $domain
     * @param string|null $localePrefix
     * @param string|null $pathPrefix
     * @param string $sslMode
     * @param bool $isDefault
     */
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

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string|null
     */
    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    /**
     * @return string|null
     */
    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    /**
     * @return string
     */
    public function getSslMode(): string
    {
        return $this->sslMode;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
