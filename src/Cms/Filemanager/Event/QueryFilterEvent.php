<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\Filemanager\CollectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class QueryFilterEvent extends Event
{
    /**
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @param CollectionInterface $collection
     * @param string $scope
     */
    public function __construct(CollectionInterface $collection, string $scope)
    {
        $this->collection = $collection;
        $this->scope      = $scope;
    }

    /**
     * @return CollectionInterface
     */
    public function getCollection(): CollectionInterface
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
