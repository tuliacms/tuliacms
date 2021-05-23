<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Event;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class TermDeleted extends DomainEvent
{
    public static function fromTerm(Term $term): self
    {
        return new self($term->getId()->getId(), $term->getTaxonomy()->getType()->getType(), $term->getTaxonomy()->getWebsiteId(), $term->getLocale());
    }
}
