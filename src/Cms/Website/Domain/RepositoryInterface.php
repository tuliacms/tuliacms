<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Domain;

use Tulia\Cms\Website\Domain\Aggregate\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface RepositoryInterface
{
    /**
     * @param Website $website
     */
    public function save(Website $website): void;

    /**
     * @param Website $website
     */
    public function delete(Website $website): void;
}
