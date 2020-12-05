<?php

declare(strict_types=1);

namespace Tulia\Framework\I18n;

/**
 * @author Adam Banaszkiewicz
 */
interface LocaleInterface
{
    public function __toString(): string;
    public function getCode(): string;
}
