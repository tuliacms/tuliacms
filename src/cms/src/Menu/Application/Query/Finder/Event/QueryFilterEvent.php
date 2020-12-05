<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Application\Query\Finder\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Menu\Application\Query\Finder\Model\ItemCollection;

/**
 * @author Adam Banaszkiewicz
 */
class QueryFilterEvent extends Event
{
    /**
     * @var ItemCollection
     */
    protected $collection;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @param ItemCollection $collection
     * @param string $scope
     */
    public function __construct(ItemCollection $collection, string $scope)
    {
        $this->collection = $collection;
        $this->scope      = $scope;
    }

    /**
     * @return ItemCollection
     */
    public function getCollection(): ItemCollection
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
