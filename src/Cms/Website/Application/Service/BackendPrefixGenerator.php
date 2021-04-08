<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Application\Service;

/**
 * @author Adam Banaszkiewicz
 */
class BackendPrefixGenerator
{
    /**
     * @var array
     */
    protected static $consonants = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'w', 'z'];

    /**
     * @var array
     */
    protected static $vowels = ['a', 'e', 'i', 'o', 'u', 'y'];

    /**
     * @param int $length
     *
     * @return string
     */
    public static function generate(int $length = 6): string
    {
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            if ($i % 2 === 0) {
                $result .= static::$consonants[rand(0, \count(static::$consonants) - 1)];
            } else {
                $result .= static::$vowels[rand(0, \count(static::$vowels) - 1)];
            }
        }

        return $result;
    }
}
