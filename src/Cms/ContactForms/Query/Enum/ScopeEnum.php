<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Query\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class ScopeEnum
{
    /**
     * Frontend, single node page.
     */
    public const SINGLE = 'single';

    /**
     * Frontend, listing page. Taxonomy page.
     */
    public const LISTING = 'listing';

    /**
     * Backend, nodes listing.
     */
    public const BACKEND_LISTING = 'backend.listing';

    /**
     * Backend, single node fetch, like edit/update/delete node page.
     */
    public const BACKEND_SINGLE = 'backend.single';

    public const INTERNAL = 'internal';

    public const SEARCH = 'search';
}
