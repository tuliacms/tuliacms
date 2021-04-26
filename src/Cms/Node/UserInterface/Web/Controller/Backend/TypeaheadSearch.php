<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Controller\Backend;

use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
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
            'search'    => $request->query->get('q'),
            'node_type' => $request->query->get('node_type'),
            'per_page'  => 10,
        ]);
        $finder->fetchRaw();

        return $finder->getResult()->toArray();
    }
}
