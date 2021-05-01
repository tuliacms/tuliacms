<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website;

use Tulia\Component\Routing\Website\Exception\LocaleNotExistsException;
use Tulia\Component\Routing\Website\Locale\LocaleInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface CurrentWebsiteInterface
{
    public function set(WebsiteInterface $website): void;
    public function get(): WebsiteInterface;
    public function has(): bool;
    public function getId(): string;
    public function getName(): string;
    public function getPathPrefix(): ?string;
    public function getLocalePrefix(): ?string;
    public function getBackendPrefix(): string;
    public function getDomain(): string;
    public function getScheme(): string;
    public function getLocales(): iterable;
    public function getDefaultLocale(): LocaleInterface;
    public function getLocale(): LocaleInterface;
    public function getAddress(): string;
    public function getBackendAddress(): string;
    public function isActive(): bool;

    /**
     * @param string $code
     *
     * @return LocaleInterface
     *
     * @throws LocaleNotExistsException
     */
    public function getLocaleByCode(string $code): LocaleInterface;
}
