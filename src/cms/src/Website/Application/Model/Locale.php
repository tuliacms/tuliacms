<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Model;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Cms\Website\Query\Model\Locale as QueryModelLocale;
use Tulia\Cms\Website\Domain\Aggregate\Locale as Aggregate;

/**
 * @author Adam Banaszkiewicz
 */
class Locale
{
    /**
     * @var null|string
     */
    protected $code;

    /**
     * @var null|string
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
    protected $isDefault = false;

    /**
     * @param string|null $code
     * @param string|null $domain
     * @param string|null $localePrefix
     * @param string|null $pathPrefix
     * @param string $sslMode
     * @param bool $isDefault
     */
    public function __construct(
        string $code = null,
        string $domain = null,
        string $localePrefix = null,
        string $pathPrefix = null,
        string $sslMode = SslModeEnum::ALLOWED_BOTH,
        bool $isDefault = false
    ) {
        $this->code = $code;
        $this->domain = $domain;
        $this->localePrefix = $localePrefix;
        $this->pathPrefix = $pathPrefix;
        $this->sslMode = $sslMode;
        $this->isDefault = $isDefault;
    }

    public static function fromQueryModel(QueryModelLocale $locale): self
    {
        return new self(
            $locale->getCode(),
            $locale->getDomain(),
            $locale->getLocalePrefix(),
            $locale->getPathPrefix(),
            $locale->getSslMode(),
            $locale->isDefault()
        );
    }

    public function __toString(): string
    {
        return (string) $this->code;
    }

    public function produceAggregate(): Aggregate
    {
        return new Aggregate(
            $this->getCode(),
            $this->getDomain(),
            $this->getLocalePrefix(),
            $this->getPathPrefix(),
            $this->getSslMode(),
            $this->isDefault()
        );
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param null|string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return null|string
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param null|string $domain
     */
    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @return string|null
     */
    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    /**
     * @param string|null $localePrefix
     */
    public function setLocalePrefix(?string $localePrefix): void
    {
        $this->localePrefix = $localePrefix;
    }

    /**
     * @return string|null
     */
    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    /**
     * @param string|null $pathPrefix
     */
    public function setPathPrefix(?string $pathPrefix): void
    {
        $this->pathPrefix = $pathPrefix;
    }

    /**
     * @return string
     */
    public function getSslMode(): string
    {
        return $this->sslMode;
    }

    /**
     * @param string $sslMode
     */
    public function setSslMode(string $sslMode): void
    {
        $this->sslMode = $sslMode;
    }

    /**
     * @return bool
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     */
    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }
}
