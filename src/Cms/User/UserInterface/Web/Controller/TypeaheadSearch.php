<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\UserMetadataEnum;
use Tulia\Cms\User\Query\Enum\ScopeEnum;
use Tulia\Cms\User\Query\FinderFactoryInterface;
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
            'search'   => $request->query->get('q'),
            'per_page' => 10,
        ]);
        $finder->fetchRaw();
        $result = [];

        foreach ($finder->getResult() as $row)
        {
            $username = $row->getUsername();

            if ($row->getMeta(UserMetadataEnum::NAME)) {
                $username = $row->getMeta(UserMetadataEnum::NAME) . " ({$username})";
            }

            $result[] = ['username' => $username];
        }

        return $result;
    }
}
