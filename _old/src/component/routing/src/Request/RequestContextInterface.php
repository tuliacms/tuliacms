<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Request;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface RequestContextInterface
{
    public function getWebsite(): CurrentWebsiteInterface;

    public function setWebsite(CurrentWebsiteInterface $website): void;

    public function getMethod(): string;

    public function setMethod(string $method): void;

    public function getScheme(): string;

    public function setScheme(string $scheme): void;

    public function getHost(): string;

    public function setHost(string $host): void;

    public function getPathinfo(): string;

    public function setPathinfo(string $pathinfo): void;

    public function getContentPath(): string;

    public function setContentPath(string $contentPath): void;

    public function getLocale(): string;

    public function setLocale(string $locale): void;

    public function getContentLocale(): string;

    public function setContentLocale(string $contentLocale): void;

    public function getDefaultLocale(): string;

    public function setDefaultLocale(string $defaultLocale): void;

    public function isBackend(): bool;

    public function setBackend(bool $backend): void;
}
