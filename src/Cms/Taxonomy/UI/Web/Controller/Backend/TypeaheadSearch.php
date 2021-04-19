<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UI\Web\Controller\Backend;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class TypeaheadSearch extends TypeaheadFormTypeSearch
{
    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    protected function findCollection(Request $request): array
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::INTERNAL);
        $finder->setCriteria([
            'search'        => $request->query->get('q'),
            'taxonomy_type' => $request->query->get('taxonomy_type'),
            'per_page'      => 10,
        ]);
        $finder->fetchRaw();

        return $finder->getResult()->toArray();
    }
}
