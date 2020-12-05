<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website\Locale;

use Tulia\Component\Routing\Enum\SslModeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class Locale implements LocaleInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $region;

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

        if (strpos($code, '_') !== false) {
            list($this->language, $this->region) = explode('_', $code);
        } else {
            $this->language = $this->region = $code;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getSslMode(): string
    {
        return $this->sslMode;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathPrefix(): ?string
    {
        return $this->pathPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalePrefix(): ?string
    {
        return $this->localePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }
}
