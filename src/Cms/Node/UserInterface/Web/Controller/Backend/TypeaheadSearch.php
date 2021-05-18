<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Controller\Backend;

use Tulia\Cms\Node\Domain\ReadModel\Finder\Enum\ScopeEnum;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\NodeFinderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class TypeaheadSearch extends TypeaheadFormTypeSearch
{
    private NodeFinderInterface $nodeFinder;

    public function __construct(NodeFinderInterface $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    protected function findCollection(Request $request): array
    {
        $nodes = $this->nodeFinder->find([
            'search' => $request->query->get('q'),
            'node_type' => $request->query->get('node_type'),
            'per_page' => 10,
        ], ScopeEnum::INTERNAL);

        $result = [];

        foreach ($nodes as $node) {
            $result[] = [
                'id' => $node->getId(),
                'title' => $node->getTitle(),
            ];
        }

        return $result;
    }
}
