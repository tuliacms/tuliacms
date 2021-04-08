<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Query;

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\ContactForms\Query\Model\Collection;
use Tulia\Cms\ContactForms\Query\Model\Form;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FinderFactory implements FinderFactoryInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @var string
     */
    protected $queryClass;

    /**
     * @param ConnectionInterface $connection
     * @param EventDispatcherInterface $eventDispatcher
     * @param CurrentWebsiteInterface $currentWebsite
     * @param string $queryClass
     */
    public function __construct(
        ConnectionInterface $connection,
        EventDispatcherInterface $eventDispatcher,
        CurrentWebsiteInterface $currentWebsite,
        string $queryClass
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
