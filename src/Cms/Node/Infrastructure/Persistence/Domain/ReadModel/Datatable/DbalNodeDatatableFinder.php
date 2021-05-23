<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use PDO;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeDatatableFinder extends AbstractDatatableFinder implements NodeDatatableFinderInterface
{
    private ?string $nodeType = null;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite
    ) {
        parent::__construct($connection, $currentWebsite);
    }

    public function setNodeType(string $nodeType): void
    {
        $this->nodeType = $nodeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationKey(): string
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns(): array
    {
        return [
            'id' => [
                'selector' => 'tm.id',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'title' => [
                'selector' => 'COALESCE(tl.title, tm.title)',
                'label' => 'title',
                'html_attr' => ['class' => 'col-title'],
                'view' => '@backend/node/parts/datatable/title.tpl',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->from('#__node', 'tm')
            ->addSelect('tm.type, tm.level, tm.parent_id, tm.slug, tm.status')
            ->leftJoin('tm', '#__node_lang', 'tl', 'tm.id = tl.node_id AND tl.locale = :locale')
            ->where('tm.type = :type')
            ->setParameter('type', $this->nodeType, PDO::PARAM_STR)
            ->setParameter('locale', $this->currentWebsite->getLocale()->getCode(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
        ;

        if ($this->currentWebsite->getDefaultLocale()->getCode() !== $this->currentWebsite->getLocale()->getCode()) {
            $queryBuilder->addSelect('IF(ISNULL(tl.title), 0, 1) AS translated');
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/node/parts/datatable/edit-link.tpl',
            'delete' => '@backend/node/parts/datatable/delete-link.tpl',
        ];
    }
}
