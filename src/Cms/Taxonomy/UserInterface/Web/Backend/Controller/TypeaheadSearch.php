<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Enum\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermFinderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class TypeaheadSearch extends TypeaheadFormTypeSearch
{
    private TermFinderInterface $termFinder;

    public function __construct(TermFinderInterface $termFinder)
    {
        $this->termFinder = $termFinder;
    }

    protected function findCollection(Request $request): array
    {
        $terms = $this->termFinder->find([
            'search'        => $request->query->get('q'),
            'taxonomy_type' => $request->query->get('taxonomy_type'),
            'per_page'      => 10,
        ], TermFinderScopeEnum::INTERNAL);

        $result = [];

        foreach ($terms as $node) {
            $result[] = [
                'id' => $node->getId(),
                'name' => $node->getName(),
            ];
        }

        return $result;
    }
}
