<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Exception;

/**
 * @author Adam Banaszkiewicz
 */
class ViewNotFoundException extends Exception
{
    public static function anyViewNotFound(array $searchedFor)
    {
        return new self("Any view not found. Searched for: '".implode("', '", $searchedFor)."'.");
    }
}
