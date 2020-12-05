<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Query\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Widget\Query\Model\Collection;

/**
 * @author Adam Banaszkiewicz
 */
class QueryFilterEvent extends Event
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @param Collection $collection
     * @param string $scope
     */
    public function __construct(Collection $collection, string $scope)
    {
        $this->collection = $collection;
        $this->scope      = $scope;
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param array $scopes
     *
     * @return bool
     */
    public function hasScope(array $scopes): bool
    {
        return \in_array($this->scope, $scopes, true);
    }
}
