<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain;

use Tulia\Cms\Widget\Domain\ValueObject\AggregateId;
use Tulia\Cms\Widget\Domain\Aggregate\Widget;
use Tulia\Cms\Widget\Domain\Exception\WidgetNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface RepositoryInterface
{
    /**
     * @param AggregateId $id
     * @param string $locale
     *
     * @return Widget
     *
     * @throws WidgetNotFoundException
     */
    public function find(AggregateId $id, string $locale): Widget;

    /**
     * @param Widget $node
     */
    public function save(Widget $node): void;

    /**
     * @param Widget $node
     */
    public function delete(Widget $node): void;
}
