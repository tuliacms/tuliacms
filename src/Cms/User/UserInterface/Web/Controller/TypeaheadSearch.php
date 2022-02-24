<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\TypeaheadFormTypeSearch;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class TypeaheadSearch extends TypeaheadFormTypeSearch
{
    private UserFinderInterface $userFinder;

    public function __construct(UserFinderInterface $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    protected function findCollection(Request $request): array
    {
        $users = $this->userFinder->find([
            'search'   => $request->query->get('q'),
            'per_page' => 10,
        ], UserFinderScopeEnum::INTERNAL);

        $result = [];

        foreach ($users as $row) {
            $username = $row->getEmail();

            if ($row->attribute('name')) {
                $username = $row->attribute('name') . " ({$username})";
            }

            $result[] = ['username' => $username];
        }

        return $result;
    }
}
