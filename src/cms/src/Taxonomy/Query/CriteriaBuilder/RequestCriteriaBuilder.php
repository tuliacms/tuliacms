<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query\CriteriaBuilder;

use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class RequestCriteriaBuilder
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $defaults
     *
     * @return array
     */
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