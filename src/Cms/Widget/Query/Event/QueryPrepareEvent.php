<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Query\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Adam Banaszkiewicz
 */
class QueryPrepareEvent extends Event
{
    /**
     * @var array
     */
    protected $criteria = [];

    /**
     * @var string
     */
    protected $scope;

    /**
     * @param array $criteria
     * @param string $scope
     */
    public function __construct(array $criteria, string $scope)
    {
        $this->criteria = $criteria;
        $this->scope    = $scope;
    }

    /**
     * @return array
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * @param array $criteria
     */
    public function setCriteria(array $criteria): void
    {
        $this->criteria = $criteria;
    }

    /**
     * @param array $criteria
     */
    public function addCriteria(array $criteria): void
    {
        $this->criteria = array_merge($this->criteria, $criteria);
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
