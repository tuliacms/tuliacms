<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\SearchAnything\Ports\SearchEngine\SearchEngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Search extends AbstractController
{
    private SearchEngineInterface $searchEngine;

    public function __construct(SearchEngineInterface $searchEngine)
    {
        $this->searchEngine = $searchEngine;
    }

    public function providers(): JsonResponse
    {
        $providers = $this->searchEngine->getProviders();
        $ids = [];

        foreach ($providers as $provider) {
            $ids[] = $provider->getId();
        }

        return new JsonResponse($ids);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $provider = $request->query->get('p');

        if (empty($provider)) {
            return new JsonResponse([]);
        }

        $result = $this->searchEngine->searchInProvider($provider, $query);
        $flatResult = $result->toArray();
        $flatResult['label'] = $this->trans(...$flatResult['label']);

        return new JsonResponse($flatResult);
    }

    public function noop(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
