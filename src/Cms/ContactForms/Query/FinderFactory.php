<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Query\DbalQuery;
use Tulia\Cms\ContactForms\Query\Model\Collection;
use Tulia\Cms\ContactForms\Query\Model\Form;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    protected ConnectionInterface $connection;
    protected EventDispatcherInterface $eventDispatcher;
    protected CurrentWebsiteInterface $currentWebsite;
    protected string $queryClass;

    public function __construct(
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite,
        string $queryClass = DbalQuery::class
    ) {
        $this->connection      = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->currentWebsite  = $currentWebsite;
        $this->queryClass      = $queryClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(string $scope, array $params = []): FinderInterface
    {
        $finder = new Finder($this->connection, $this->queryClass, array_merge([
            'website' => $this->currentWebsite->getId(),
            'locale'  => $this->currentWebsite->getLocale()->getCode(),
            'default_locale' => $this->currentWebsite->getDefaultLocale()->getCode(),
            'scope'   => $scope,
        ], $params));

        $finder->setEventDispatcher($this->eventDispatcher);

        return $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(array $criteria, string $scope): Collection
    {
        $finder = $this->getInstance($scope);
        $finder->setCriteria($criteria);
        $finder->fetch();
        return $finder->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $id, string $scope): ?Form
    {
        return $this->getInstance($scope)->find($id);
    }
}
