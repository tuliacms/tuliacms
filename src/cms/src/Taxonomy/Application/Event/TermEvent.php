<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Taxonomy\Application\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class TermEvent extends Event
{
    /**
     * @var Term
     */
    protected $term;

    /**
     * @param Term $term
     */
    public function __construct(Term $term)
    {
        $this->term = $term;
    }

    /**
     * @return Term
     */
    public function getTerm(): Term
    {
        return $this->term;
    }
}
