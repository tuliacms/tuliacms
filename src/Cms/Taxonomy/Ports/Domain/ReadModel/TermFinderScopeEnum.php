<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Ports\Domain\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
class TermFinderScopeEnum
{
    /**
     * Frontend, taxonomy listing (like category or tag page).
     */
    public const TAXONOMY_LISTING = 'taxonomy.taxonomy.listing';

    /**
     * Frontend, single node page.
     */
    public const SINGLE = 'taxonomy.single';

    public const API_LISTING = 'taxonomy.api.listing';

    /**
     * Backend, nodes listing.
     */
    public const BACKEND_LISTING = 'taxonomy.backend.listing';

    /**
     * Backend, single node fetch, like edit/update/delete node page.
     */
    public const BACKEND_SINGLE = 'taxonomy.backend.single';

    public const ROUTING_GENERATOR = 'taxonomy.routing.generator';
    public const ROUTING_MATCHER = 'taxonomy.routing.matcher';
    public const BREADCRUMBS = 'taxonomy.breadcrumbs';

    public const INTERNAL = 'taxonomy.internal';

    public const SEARCH = 'taxonomy.search';
}
