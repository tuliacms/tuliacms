<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\ReadModel\Finder;

use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
interface ContactFormFinderInterface
{
    /**
     * @param array $criteria
     * @param string $scope
     * @return null|Form
     */
    public function findOne(array $criteria, string $scope);

    /**
     * @return Collection|Form[]
     */
    public function find(array $criteria, string $scope): Collection;
}
