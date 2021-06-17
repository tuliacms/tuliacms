<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Ports\Domain\ReadModel;

use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\Model\Form;
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

    public function find(array $criteria, string $scope): Collection;
}
