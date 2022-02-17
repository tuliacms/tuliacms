<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Dbal\ReadModel;

use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Node\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeDatatableFinder extends AbstractDatatableFinder implements NodeDatatableFinderInterface
{
    private ContentType $contentType;

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

    public function setContentType(ContentType $contentType): void
    {
        $this->contentType = $contentType;
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
        $context = [
            'contentType' => $this->contentType,
        ];

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
                'view_context' => $context,
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
            ->addSelect('tm.type, tm.level, tm.parent_id, tm.slug, tm.status, GROUP_CONCAT(tnhf.flag SEPARATOR \',\') AS flags')
            ->leftJoin('tm', '#__node_lang', 'tl', 'tm.id = tl.node_id AND tl.locale = :locale')
            ->leftJoin('tm', '#__node_has_flag', 'tnhf', 'tm.id = tnhf.node_id')
            ->where('tm.type = :type AND tm.website_id = :website_id')
            ->setParameter('type', $this->contentType->getCode(), PDO::PARAM_STR)
            ->setParameter('locale', $this->currentWebsite->getLocale()->getCode(), PDO::PARAM_STR)
            ->setParameter('website_id', $this->currentWebsite->getId(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
            ->addGroupBy('tm.id')
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

    public function prepareResult(array $result): array
    {
        foreach ($result as $key => $row) {
            $result[$key]['flags'] = array_filter(explode(',', (string) $row['flags']));
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        $context = [
            'contentType' => $this->contentType,
        ];

        return [
            'main' => [
                'view' => '@backend/node/parts/datatable/links/edit-link.tpl',
                'view_context' => $context,
            ],
            'preview' => [
                'view' => '@backend/node/parts/datatable/links/preview-link.tpl',
                'view_context' => $context,
            ],
            'delete' => [
                'view' => '@backend/node/parts/datatable/links/delete-link.tpl',
                'view_context' => $context,
            ],
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
        // @todo
        return false;
        foreach ($this->contentType->getTaxonomies() as $taxonomy) {
            if ($taxonomy['taxonomy'] === 'category') {
                return true;
            }
        }

        return false;
    }

    private function createStatusChoices(): array
    {
        // @todo
        return [];
        $statuses = [];

        foreach ($this->contentType->getStatuses() as $status) {
            $statuses[$status] = $this->translator->trans($status, [], 'node');
        }

        return $statuses;
    }
}
