<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Query\CriteriaBuilder;

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
            'per_page' => 15,
        ], $defaults);

        $space = $this->request->query->get('space', null);

        /**
         * Force null space, when no provided fo fetch all Widgets.
         */
        if (!$space) {
            $space = null;
        }

        return [
            'space'    => $space,
            'per_page' => $defaults['per_page'],
        ];
    }
}
