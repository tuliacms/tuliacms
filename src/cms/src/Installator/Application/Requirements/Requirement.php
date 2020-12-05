<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\Application\Requirements;

/**
 * @author Adam Banaszkiewicz
 */
class Requirement
{
    /**
     * Status for some required module/setting,
     * without it system is not able to work.
     */
    public const STATUS_REQUIRE = 1;

    /**
     * Some requirement is almost passed, but system will be albe to work.
     * Used for to small allowed memory in example.
     */
    public const STATUS_WARNING = 2;

    /**
     * Requirement passed.
     */
    public const STATUS_PASSED = 10;

    public $name;
    public $status;
    public $cause;
    public $solution;
}
