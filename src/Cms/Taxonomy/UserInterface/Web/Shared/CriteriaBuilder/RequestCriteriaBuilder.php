<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Shared\CriteriaBuilder;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class RequestCriteriaBuilder
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function build(array $defaults = []): array
    {
        $defaults = array_merge([
            'per_page'  => 15,
        ], $defaults);

        $queryString   = $this->request->query->get('q', null);

        $criteria = [
            'taxonomy_type' => $defaults['taxonomy_type'] ?? null,
            'per_page' => $defaults['per_page'],
            'search'   => $queryString,
        ];

        return $criteria;
    }
}
