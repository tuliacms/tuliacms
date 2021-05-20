<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Shared\CriteriaBuilder;

use Symfony\Component\HttpFoundation\Request;

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

        $status        = $this->request->query->get('node_status', null);
        $taxonomy      = $this->request->query->get('taxonomy', null);
        $taxonomyTerm  = $this->request->query->get('taxonomy_term', null);
        $taxonomyField = $this->request->query->get('taxonomy_field', null);
        $queryString   = $this->request->query->get('q', null);

        /**
         * Force null node_status when there is any empty data (empty string or 0 (zero) too).
         * When user goes to empty query string for 'node_status' var, query tries to fetch nodes
         * with empty type, and we dont want this!
         */
        if (!$status) {
            $status = null;
        }

        $criteria = [
            'node_type'   => $defaults['node_type'] ?? null,
            'node_status' => $status,
            'per_page'    => $defaults['per_page'],
            'search'      => $queryString,
        ];

        if ($taxonomy && $taxonomyTerm) {
            $criteria['taxonomy'] = [[
                'taxonomy' => $taxonomy,
                'field'    => $taxonomyField ?? 'term_id',
                'terms'    => $taxonomyTerm,
            ]];
        }

        return $criteria;
    }
}
