<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\Exception\WebsiteNotResolvedException;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CurrentWebsite implements CurrentWebsiteInterface
{
    protected ?WebsiteInterface $currentWebsite = null;

    public function set(WebsiteInterface $website): void
    {
        $this->currentWebsite = $website;
    }

    public function get(): WebsiteInterface
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite;
    }

    public function has(): bool
    {
        return $this->currentWebsite !== null;
    }

    public function getId(): string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getId();
    }

    public function getName(): string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getName();
    }

    public function getPathPrefix(): ?string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getLocale()->getPathPrefix();
    }

    public function getLocalePrefix(): ?string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getLocale()->getLocalePrefix();
    }

    public function getBackendPrefix(): string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getBackendPrefix();
    }

    public function getDomain(): string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getLocale()->getDomain();
    }

    public function getScheme(): string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getLocale()->getSslMode() === SslModeEnum::FORCE_SSL ? 'https' : 'http';
    }

    public function getLocales(): iterable
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getLocales();
    }

    public function getLocale(): LocaleInterface
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getLocale();
    }

    public function getDefaultLocale(): LocaleInterface
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getDefaultLocale();
    }

    public function getAddress(): string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getAddress();
    }

    public function getBackendAddress(): string
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getBackendAddress();
    }

    public function isActive(): bool
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->isActive();
    }

    public function getLocaleByCode(string $code): LocaleInterface
    {
        $this->ensureWebsiteIsset();

        return $this->currentWebsite->getLocaleByCode($code);
    }

    private function ensureWebsiteIsset(): void
    {
        if ($this->currentWebsite === null) {
            $message = 'for this Request. Did You forget to configure this domain for Website in system?';

            if ($this->isCommandLineInterface()) {
                $message = 'for this command, and system requires it to do the job. Did You forget to add a "website" argument to console command?';
            }

            throw new WebsiteNotResolvedException('Website is not resolved '.$message);
        }
    }

    private function isCommandLineInterface(): bool
    {
        return php_sapi_name() === 'cli';
    }
}
