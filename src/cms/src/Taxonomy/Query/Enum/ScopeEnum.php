<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class ScopeEnum
{
    /**
     * Frontend, taxonomy listing (like category or tag page).
     */
    public const TAXONOMY_LISTING = 'taxonomy.listing';

    /**
     * Frontend, single node page.
     */
    public const SINGLE = 'single';

    public const API_LISTING = 'api.listing';

    /**
     * Backend, nodes listing.
     */
    public const BACKEND_LISTING = 'backend.listing';

    /**
     * Backend, single node fetch, like edit/update/delete node page.
     */
    public const BACKEND_SINGLE = 'backend.single';

    public const ROUTING_GENERATOR = 'routing.generator';
    public const ROUTING_MATCHER = 'routing.matcher';
    public const BREADCRUMBS = 'breadcrumbs';

    public const INTERNAL = 'internal';

    public const SEARCH = 'search';
}
