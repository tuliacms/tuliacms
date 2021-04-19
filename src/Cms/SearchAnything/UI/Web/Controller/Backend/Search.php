<?php

declare(strict_types=1);

namespace Tulia\Cms\SearchAnything\UI\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\JsonResponse;
use Tulia\Cms\SearchAnything\Factory\EngineFactoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Search extends AbstractController
{
    public function providers(EngineFactoryInterface $engineFactory): JsonResponse
    {
        $providers = $engineFactory->getProviders();
        $ids = [];

        foreach ($providers as $provider) {
            $ids[] = $provider->getId();
        }

        return new JsonResponse($ids);
    }

    public function search(Request $request, EngineFactoryInterface $engineFactory): JsonResponse
    {
        $query    = $request->query->get('q');
        $provider = $request->query->get('p');

        if (empty($provider)) {
            return new JsonResponse([]);
        }

        $engine = $engineFactory->providerEngine($provider);
        $result = $engine->search($query);
        $flatResult = $result->toArray();
        $flatResult['label'] = $this->trans(...$flatResult['label']);

        return new JsonResponse($flatResult);
    }

    public function noop(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
