<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Node\Domain\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Enum\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermFinderInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeDatatableFinder extends AbstractDatatableFinder implements NodeDatatableFinderInterface
{
    private NodeTypeInterface $nodeType;

    private TermFinderInterface $termFinder;
    private TranslatorInterface $translator;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        TermFinderInterface $termFinder,
        TranslatorInterface $translator
    ) {
        parent::__construct($connection, $currentWebsite);
        $this->termFinder = $termFinder;
        $this->translator = $translator;
    }

    public function setNodeType(NodeTypeInterface $nodeType): void
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
        $columns = [
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
            'published_at' => [
                'selector' => 'tm.published_at',
                'label' => 'date',
                'html_attr' => ['class' => 'col-date'],
                'view' => '@backend/node/parts/datatable/published.tpl',
            ],
        ];

        if ($this->supportsCategoryTaxonomy()) {
            $columns['category'] = [
                'selector' => 'COALESCE(nt.name, ntl.name)',
                'html_attr' => ['class' => 'text-center'],
            ];
        }

        return $columns;
    }

    public function getFilters(): array
    {
        $filters = [];

        $filters['title'] = [
            'label' => 'title',
            'type' => 'text',
            'selector' => 'COALESCE(tl.title, tm.title)'
        ];

        if ($this->supportsCategoryTaxonomy()) {
            $filters['category'] = [
                'label' => 'category',
                'type' => 'single_select',
                'choices' => $this->createTaxonomyChoices(),
                'selector' => 'nt.id'
            ];
        }

        $filters['status'] = [
            'label' => 'status',
            'type' => 'single_select',
            'choices' => $this->createStatusChoices(),
            'selector' => 'tm.status'
        ];

        return $filters;
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
            ->setParameter('type', $this->nodeType->getType(), PDO::PARAM_STR)
            ->setParameter('locale', $this->currentWebsite->getLocale()->getCode(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
        ;

        if ($this->currentWebsite->getDefaultLocale()->getCode() !== $this->currentWebsite->getLocale()->getCode()) {
            $queryBuilder->addSelect('IF(ISNULL(tl.title), 0, 1) AS translated');
        }

        if ($this->supportsCategoryTaxonomy()) {
            $queryBuilder
                ->addSelect('nt.id AS term_id, nt.type AS taxonomy_type')
                ->leftJoin('tm', '#__node_term_relationship', 'ntr', 'ntr.node_id = tm.id')
                ->leftJoin('ntr', '#__term', 'nt', 'nt.id = ntr.term_id')
                ->leftJoin('nt', '#__term_lang', 'ntl', 'ntl.term_id = nt.id AND ntl.locale = :locale');
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

    private function createTaxonomyChoices(): array
    {
        $terms = $this->termFinder->find([
            'sort_hierarchical' => true,
        ], TermFinderScopeEnum::INTERNAL);

        $result = [];

        foreach ($terms as $term) {
            $result[$term->getId()] = str_repeat('- ', $term->getLevel() - 1) . $term->getName();
        }

        return $result;
    }

    private function supportsCategoryTaxonomy(): bool
    {
        foreach ($this->nodeType->getTaxonomies() as $taxonomy) {
            if ($taxonomy['taxonomy'] === 'category') {
                return true;
            }
        }

        return false;
    }

    private function createStatusChoices(): array
    {
        $statuses = [];

        foreach ($this->nodeType->getStatuses() as $status) {
            $statuses[$status] = $this->translator->trans($status, [], $this->nodeType->getTranslationDomain());
        }

        return $statuses;
    }
}
