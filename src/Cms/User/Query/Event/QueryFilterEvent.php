<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Query\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\User\Query\Model\Collection;
use Tulia\Cms\User\Query\Model\User;

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
     * @return Collection|User[]
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
