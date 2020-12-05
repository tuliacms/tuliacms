<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Query\Factory;

use Tulia\Cms\Taxonomy\Query\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
interface TermFactoryInterface
{
    /**
     * @param array $data
     *
     * @return Term
     */
    public function createNew(array $data = []): Term;
}
