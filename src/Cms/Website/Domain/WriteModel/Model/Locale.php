<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Model;

use Tulia\Component\Routing\Enum\SslModeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class Locale
{
    protected string $code;
    protected string $domain;
    protected ?string $domainDevelopment = null;
    protected ?string $localePrefix = null;
    protected ?string $pathPrefix = null;
    protected string $sslMode;
    protected bool $isDefault = false;

    public function __construct(
        string $code,
        string $domain,
        string $domainDevelopment = null,
        string $localePrefix = null,
        string $pathPrefix = null,
        string $sslMode = SslModeEnum::ALLOWED_BOTH,
        bool $isDefault = false
    ) {
        $this->code = $code;
        $this->domainDevelopment = $domainDevelopment;
        $this->domain = $domain;
        $this->localePrefix = $localePrefix;
        $this->pathPrefix = $pathPrefix;
        $this->sslMode = $sslMode;
        $this->isDefault = $isDefault;
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
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

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }
}
