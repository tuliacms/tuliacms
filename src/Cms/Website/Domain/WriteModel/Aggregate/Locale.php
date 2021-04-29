<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\WriteModel\Aggregate;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Cms\Website\Domain\ReadModel\Model\Locale as QueryModelLocale;

/**
 * @author Adam Banaszkiewicz
 */
class Locale
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
    protected $isDefault = false;

    /**
     * @param string $code
     * @param string $domain
     * @param string|null $localePrefix
     * @param string|null $pathPrefix
     * @param string $sslMode
     * @param bool $isDefault
     */
    public function __construct(
        string $code,
        string $domain,
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

    public function __toString(): string
    {
        return $this->code;
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
}
