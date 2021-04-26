<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable\Filter;

/**
 * @author Adam Banaszkiewicz
 */
class ComparisonOperatorsEnum
{
    public const HAS = 'HAS';
    public const EQUAL = 'EQUAL';
    public const LESS = 'LESS';
    public const LESS_EQUAL = 'LESS_EQUAL';
    public const MORE = 'MORE';
    public const MORE_EQUAL = 'MORE_EQUAL';

    public static function all(): array
    {
        return [
            self::HAS,
            self::EQUAL,
            self::LESS,
            self::LESS_EQUAL,
            self::MORE,
            self::MORE_EQUAL,
        ];
    }
}
