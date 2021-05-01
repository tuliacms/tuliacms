<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CurrentWebsite implements CurrentWebsiteInterface
{
    protected ?WebsiteInterface $currentWebsite = null;

    /**
     * {@inheritdoc}
     */
    public function set(WebsiteInterface $website): void
    {
        $this->currentWebsite = $website;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): WebsiteInterface
    {
        return $this->currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function has(): bool
    {
        return $this->currentWebsite !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->currentWebsite->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->currentWebsite->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getPathPrefix(): ?string
    {
        return $this->currentWebsite->getLocale()->getPathPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalePrefix(): ?string
    {
        return $this->currentWebsite->getLocale()->getLocalePrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendPrefix(): string
    {
        return $this->currentWebsite->getBackendPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): string
    {
        return $this->currentWebsite->getLocale()->getDomain();
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        return $this->currentWebsite->getLocale()->getSslMode() === SslModeEnum::FORCE_SSL ? 'https' : 'http';
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales(): iterable
    {
        return $this->currentWebsite->getLocales();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): LocaleInterface
    {
        return $this->currentWebsite->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale(): LocaleInterface
    {
        return $this->currentWebsite->getDefaultLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress(): string
    {
        return $this->currentWebsite->getAddress();
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendAddress(): string
    {
        return $this->currentWebsite->getBackendAddress();
    }

    /**
     * {@inheritdoc}
     */
    public function isActive(): bool
    {
        return $this->currentWebsite->isActive();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleByCode(string $code): LocaleInterface
    {
        return $this->currentWebsite->getLocaleByCode($code);
    }
}
