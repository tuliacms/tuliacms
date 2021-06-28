<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\ReadModel\Finder;

/**
 * @author Adam Banaszkiewicz
 */
class NodeFinderScopeEnum
{
    /**
     * Frontend, taxonomy listing (like category or tag page).
     */
    public const TAXONOMY_LISTING = 'node.taxonomy.listing';

    /**
     * Frontend, single node page.
     */
    public const SINGLE = 'node.single';

    /**
     * Frontend, listing page. Taxonomy page.
     */
    public const LISTING = 'node.listing';

    public const API_LISTING = 'node.api.listing';

    /**
     * Backend, nodes listing.
     */
    public const BACKEND_LISTING = 'node.backend.listing';

    /**
     * Backend, single node fetch, like edit/update/delete node page.
     */
    public const BACKEND_SINGLE = 'node.backend.single';

    public const ROUTING_GENERATOR = 'node.routing.generator';
    public const ROUTING_MATCHER = 'node.routing.matcher';
    public const BREADCRUMBS = 'node.breadcrumbs';

    public const INTERNAL = 'node.internal';

    public const SEARCH = 'node.search';
}
