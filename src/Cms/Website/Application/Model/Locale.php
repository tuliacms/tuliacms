<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Model;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Cms\Website\Domain\ReadModel\Finder\Model\Locale as QueryModelLocale;
use Tulia\Cms\Website\Domain\WriteModel\Aggregate\Locale as Aggregate;

/**
 * @author Adam Banaszkiewicz
 */
class Locale
{
    protected ?string $code = null;
    protected ?string $domain = null;
    protected ?string $domainDevelopment = null;
    protected ?string $localePrefix = null;
    protected ?string $pathPrefix = null;
    protected string $sslMode;
    protected bool $isDefault = false;

    public function __construct(
        string $code = null,
        string $domain = null,
        string $domainDevelopment = null,
        string $localePrefix = null,
        string $pathPrefix = null,
        string $sslMode = SslModeEnum::ALLOWED_BOTH,
        bool $isDefault = false
    ) {
        $this->code = $code;
        $this->domain = $domain;
        $this->domainDevelopment = $domainDevelopment;
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
            '',//$locale->getDomainDevelopment(),
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
            //$this->getDomainDevelopment(),
            $this->getLocalePrefix(),
            $this->getPathPrefix(),
            $this->getSslMode(),
            $this->isDefault()
        );
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }

    public function getDomainDevelopment(): ?string
    {
        return $this->domainDevelopment;
    }

    public function setDomainDevelopment(?string $domainDevelopment): void
    {
        $this->domainDevelopment = $domainDevelopment;
    }

    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    public function setLocalePrefix(?string $localePrefix): void
    {
        $this->localePrefix = $localePrefix;
    }

    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    public function setPathPrefix(?string $pathPrefix): void
    {
        $this->pathPrefix = $pathPrefix;
    }

    public function getSslMode(): string
    {
        return $this->sslMode;
    }

    public function setSslMode(string $sslMode): void
    {
        $this->sslMode = $sslMode;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }
}
