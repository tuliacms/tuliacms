<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain\ReadModel\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class ScopeEnum
{
    /**
     * Backend, nodes listing.
     */
    public const BACKEND_LISTING = 'backend.listing';

    /**
     * Backend, single node fetch, like edit/update/delete node page.
     */
    public const BACKEND_SINGLE = 'backend.single';

    /**
     * Internal usage. Should not be modified by any plugins/listeners.
     */
    public const INTERNAL = 'internal';
}
