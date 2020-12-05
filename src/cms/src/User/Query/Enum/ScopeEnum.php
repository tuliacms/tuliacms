<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class ScopeEnum
{
    /**
     * Backend, nodes listing
     */
    public const BACKEND_LISTING = 'backend.list';

    /**
     * Backend, nodes listing
     */
    public const BACKEND_SINGLE = 'backend.single';

    /**
     * All internal places. For now used to fetch user for user() function in Twig.
     */
    public const INTERNAL = 'internal';
    public const SEARCH = 'search';
}
