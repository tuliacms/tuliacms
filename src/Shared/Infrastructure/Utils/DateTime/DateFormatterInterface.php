<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Utils\DateTime;

/**
 * @author Adam Banaszkiewicz
 */
interface DateFormatterInterface
{
    public function setFormat(string $format): void;
    public function getFormat(): string;
    public function format($date, $format = null): string;
}
