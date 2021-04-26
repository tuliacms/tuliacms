<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Website\Locale;

/**
 * @author Adam Banaszkiewicz
 */
interface LocaleInterface
{
    public function __toString(): string;
    public function getCode(): string;
    public function getLanguage(): string;
    public function getRegion(): string;
    public function getDomain(): string;
    public function getSslMode(): string;
    public function getPathPrefix(): ?string;
    public function getLocalePrefix(): ?string;
    public function isDefault(): bool;
    public function setDefault(bool $isDefault): void;
}
