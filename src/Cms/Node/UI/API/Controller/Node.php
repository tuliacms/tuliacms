<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UI\API\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\CriteriaBuilder\RequestCriteriaBuilder;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractApiController;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractApiController
{
    /**
     * @var RegistryInterface
     */
    protected $nodeRegistry;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @param RegistryInterface $nodeRegistry
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(
        RegistryInterface $nodeRegistry,
        FinderFactoryInterface $finderFactory
    )
    {
        $this->nodeRegistry  = $nodeRegistry;
        $this->finderFactory = $finderFactory;
    }

    public function list(Request $request): JsonResponse
    {
        $criteria = (new RequestCriteriaBuilder($request))->build();

        $this->findNodeType($criteria['node_type']);

        $finder = $this->finderFactory->getInstance(ScopeEnum::API_LISTING);
        $finder->setCriteria($criteria);
        $finder->fetch();

        $paginator = $finder->getPaginator($request);

        $data = $finder->getResult();

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

    /**
     * @param string $type
     *
     * @return NodeTypeInterface
     */
    protected function findNodeType(string $type): NodeTypeInterface
    {
        $nodeType = $this->nodeRegistry->getType($type);

        if (! $nodeType) {
            $this->throwPageNotFound('Node type not found.');
        }

        return $nodeType;
    }
}
