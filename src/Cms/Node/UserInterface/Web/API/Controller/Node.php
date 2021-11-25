<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\API\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\UserInterface\Web\CriteriaBuilder\RequestCriteriaBuilder;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractApiController;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractApiController
{
    private NodeTypeRegistry $nodeRegistry;

    private NodeFinderInterface $nodeFinder;

    public function __construct(
        NodeTypeRegistry $nodeRegistry,
        NodeFinderInterface $nodeFinder
    ) {
        $this->nodeRegistry = $nodeRegistry;
        $this->nodeFinder = $nodeFinder;
    }

    public function list(Request $request): JsonResponse
    {
        $criteria = (new RequestCriteriaBuilder($request))->build();

        $this->findNodeType($criteria['node_type']);

        $nodes = $this->nodeFinder->find($criteria, NodeFinderScopeEnum::API_LISTING);

        $paginator = $finder->getPaginator($request);

        $nodes = $finder->getResult();

        foreach ($data as $key => $val) {
            $data[$key]['_links'] = [
                'self' => [
                    'href' => $this->generateUrl('api.node.single.get', ['id' => $val['id']]),
                ],
            ];
        }

        return new JsonResponse([
            'data' => $data,
            '_meta' => [
                'total' => $finder->getTotalCount(),
                'pages' => $paginator->getNumPages(),
            ],
            '_links' => [
                'next' => [
                    'href' => $paginator->getNextUrl(),
                ],
                'prev' => [
                    'href' => $paginator->getPrevUrl(),
                ],
            ],
        ]);
    }

    protected function findNodeType(string $type): NodeType
    {
        $nodeType = $this->nodeRegistry->get($type);

        if (! $nodeType) {
            $this->throwPageNotFound('Node type not found.');
        }

        return $nodeType;
    }
}
