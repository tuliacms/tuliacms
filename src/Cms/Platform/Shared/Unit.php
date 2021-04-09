<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared;

/**
 * @author Adam Banaszkiewicz
 */
class Unit
{
    public static function bytesFormat(int $bytes, int $decimals = 2): string
    {
        $size = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
        $factor = floor((\strlen((string) $bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / (1024 ** $factor)) . @ $size[$factor];
    }
}
