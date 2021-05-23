<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use PDO;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\Datatable\TermDatatableFinderInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTermDatatableFinder extends AbstractDatatableFinder implements TermDatatableFinderInterface
{
    private TranslatorInterface $translator;

    private ?string $taxonomyType = null;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        TranslatorInterface $translator
    ) {
        parent::__construct($connection, $currentWebsite);

        $this->translator = $translator;
    }

    public function setTaxonomyType(string $taxonomyType): void
    {
        $this->taxonomyType = $taxonomyType;
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
            'name' => [
                'selector' => 'COALESCE(tl.name, tm.name)',
                'label' => 'name',
                'view' => '@backend/taxonomy/term/parts/datatable/name.tpl',
            ],
            'visibility' => [
                'selector' => 'COALESCE(tl.visibility, tm.visibility)',
                'label' => 'visibility',
                'value_translation' => [
                    '1' => $this->translator->trans('visible'),
                    '0' => $this->translator->trans('invisible'),
                ],
                'value_class' => [
                    '1' => 'text-success',
                    '0' => 'text-danger',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->from('#__term', 'tm')
            ->addSelect('tm.type, tm.level, tm.parent_id')
            ->leftJoin('tm', '#__term_lang', 'tl', 'tm.id = tl.term_id AND tl.locale = :locale')
            ->where('tm.type = :type')
            ->andWhere('tm.is_root = 0')
            ->setParameter('type', $this->taxonomyType, PDO::PARAM_STR)
            ->setParameter('locale', $this->currentWebsite->getLocale()->getCode(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
            ->addOrderBy('tm.position', 'ASC')
        ;

        if ($this->currentWebsite->getDefaultLocale()->getCode() !== $this->currentWebsite->getLocale()->getCode()) {
            $queryBuilder->addSelect('IF(ISNULL(tl.name), 0, 1) AS translated');
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareResult(array $result): array
    {
        return $this->sort($result);
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        return [
            'main' => '@backend/taxonomy/term/parts/datatable/links/edit-link.tpl',
            'delete' => '@backend/taxonomy/term/parts/datatable/links/delete-link.tpl',
        ];
    }

    private function sort(array $items, int $level = 1, string $parent = Term::ROOT_ID): array
    {
        $result = [];

        foreach ($items as $item) {
            $item['level'] = (int) $item['level'];
            if ($item['level'] === $level && $item['parent_id'] === $parent) {
                $result[] = [$item];
                $result[] = $this->sort($items, $level + 1, $item['id']);
            }
        }

        if ($result === []) {
            return [];
        }

        return array_merge(...$result);
    }
}
