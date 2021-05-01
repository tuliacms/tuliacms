<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Domain\ReadModel\Finder\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Adam Banaszkiewicz
 */
class QueryPrepareEvent extends Event
{
    protected array $criteria = [];
    protected string $scope;
    private array $parameters;

    public function __construct(array $criteria, string $scope, array $parameters)
    {
        $this->criteria = $criteria;
        $this->scope = $scope;
        $this->parameters = $parameters;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function setCriteria(array $criteria): void
    {
        $this->criteria = $criteria;
    }

    public function addCriteria(array $criteria): void
    {
        $this->criteria = array_merge($this->criteria, $criteria);
    }

    public function setParameters(array $parameters): array
    {
        $this->parameters = $parameters;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function hasScope(array $scopes): bool
    {
        return \in_array($this->scope, $scopes, true);
    }
}
