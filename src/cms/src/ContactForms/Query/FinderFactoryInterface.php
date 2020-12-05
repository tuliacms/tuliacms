<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Query;

use Tulia\Cms\ContactForms\Query\Model\Collection;
use Tulia\Cms\ContactForms\Query\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface FinderFactoryInterface
{
    /**
     * @param string $scope
     *
     * @return FinderInterface
     */
    public function getInstance(string $scope): FinderInterface;

    /**
     * @param string $scope
     * @param array $criteria
     *
     * @return Collection
     */
    public function fetch(array $criteria, string $scope): Collection;

    /**
     * @param string $id
     *
     * @return Form|null
     */
    public function find(string $id, string $scope): ?Form;
}
