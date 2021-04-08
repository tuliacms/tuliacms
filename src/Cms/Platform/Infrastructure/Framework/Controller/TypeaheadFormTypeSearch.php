<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
abstract class TypeaheadFormTypeSearch extends AbstractController
{
    abstract protected function findCollection(Request $request): array;

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handleSearch(Request $request): JsonResponse
    {
        return $this->responseJson([
            'status' => true,
            'result' => $this->findCollection($request),
        ]);
    }
}
