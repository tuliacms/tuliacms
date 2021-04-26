<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Utilities\DateTime;

/**
 * @author Adam Banaszkiewicz
 */
interface DateFormatterInterface
{
    public function setFormat(string $format): void;
    public function getFormat(): string;
    public function format($date, $format = null): string;
}
