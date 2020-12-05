<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain;

use Tulia\Cms\Taxonomy\Domain\ValueObject\AggregateId;
use Tulia\Cms\Taxonomy\Domain\Aggregate\Term;
use Tulia\Cms\Taxonomy\Domain\Exception\TermNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface RepositoryInterface
{
    /**
     * @param AggregateId $id
     * @param string $locale
     *
     * @return Term
     *
     * @throws TermNotFoundException
     */
    public function find(AggregateId $id, string $locale): Term;

    /**
     * @param Term $term
     */
    public function save(Term $term): void;

    /**
     * @param Term $term
     */
    public function delete(Term $term): void;
}
