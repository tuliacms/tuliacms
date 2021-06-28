<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Datatable\Filter\ComparisonOperatorsEnum;
use Tulia\Component\Datatable\Filter\Filter;
use Tulia\Component\Datatable\Filter\FilterCollectionBuilder;
use Tulia\Component\Datatable\Finder\FinderInterface;
use Tulia\Component\Datatable\Plugin\PluginInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Datatable
{
    private FinderInterface $finder;

    private Request $request;

    private TranslatorInterface $translator;

    private EngineInterface $engine;

    /**
     * @var array|PluginInterface[]
     */
    private array $plugins = [];

    public function __construct(
        FinderInterface $finder,
        Request $request,
        TranslatorInterface $translator,
        EngineInterface $engine,
        array $plugins = []
    ) {
        $this->finder = $finder;
        $this->request = $request;
        $this->translator = $translator;
        $this->engine = $engine;
        $this->plugins = $plugins;
    }

    public function addPlugin(PluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    public function generateFront(array $options): array
    {
        $front = [];
        $front['columns'] = $this->getColumns();

        if ($options['actions_column'] === true) {
            $front['columns']['actions'] = [
                'type'  => 'actions',
                'label' => $this->translator->trans('actions'),
            ];
        }

        foreach ($this->getFilters() as $name => $info) {
            $front['filters'][$name] = [
                'type'  => $info['type'] ?? 'text',
                'label' => $this->translator->trans($info['label'] ?? $name, [], $info['translation_domain'] ?? null),
                'choices' => $info['choices'] ?? [],
                'comparison' => $info['comparison'] ?? null,
            ];

            if ($front['filters'][$name]['type'] === 'yes_no') {
                if ($front['filters'][$name]['comparison'] === null) {
                    $front['filters'][$name]['comparison'] = ComparisonOperatorsEnum::EQUAL;
                }

                if (empty($front['filters'][$name]['choices'])) {
                    $front['filters'][$name]['choices'] = [
                        '1' => $this->translator->trans('yes'),
                        '0' => $this->translator->trans('no'),
                    ];
                }
            }

            if ($front['filters'][$name]['type'] === 'single_select') {
                if ($front['filters'][$name]['comparison'] === null) {
                    $front['filters'][$name]['comparison'] = ComparisonOperatorsEnum::EQUAL;
                }
            }
            if ($front['filters'][$name]['type'] === 'text') {
                if ($front['filters'][$name]['comparison'] === null) {
                    $front['filters'][$name]['comparison'] = ComparisonOperatorsEnum::HAS;
                }
            }
        }

        return $front;
    }

    public function generateResponse(): JsonResponse
    {
        $filters = (new FilterCollectionBuilder())->build(
            (array) $this->request->get('filter', []),
            $this->getFilters(),
            $this->getColumns()
        );

        $result = $this->getAll(
            $filters,
            $this->request->get('sort_by'),
            $this->request->get('sort_dir'),
            (int) $this->request->get('limit'),
            (int) $this->request->get('page')
        );

        foreach ($this->getColumns() as $column => $info) {
            foreach ($result as $key => $row) {
                if (isset($row[$column]) && isset($info['view'])) {
                    $data = array_merge($info['view_context'] ?? [], [
                        'row' => $row,
                    ]);

                    $result[$key][$column] = $this->engine->render(new View($info['view'], $data));
                }
            }
        }

        return new JsonResponse([
            'data' => $result,
            'meta' => [
                'total_rows' => $this->countAll($filters),
                'limit' => (int) $this->request->get('limit'),
                'page' => (int) $this->request->get('page'),
                'sort_by' => $this->request->get('sort_by'),
                'sort_dir' => $this->request->get('sort_dir'),
            ],
        ]);
    }

    public function getColumns(): array
    {
        $columns[] = $this->finder->getColumns();

        foreach ($this->plugins as $plugin) {
            $columns[] = $plugin->getColumns();
        }

        $columns = array_merge(...$columns);

        foreach ($columns as $name => $info) {
            $columns[$name] = [
                'type' => $info['type'] ?? 'text',
                'selector' => $info['selector'] ?? $name,
                'label' => $this->translator->trans($info['label'] ?? $name, [], $info['translation_domain'] ?? null),
                'sortable' => (bool) ($info['sortable'] ?? false),
                'html_attr' => $info['html_attr'] ?? [],
                'value_translation' => $info['value_translation'] ?? [],
                'value_class' => $info['value_class'] ?? [],
                'view' => $info['view'] ?? null,
                'view_context' => $info['view_context'] ?? [],
            ];
        }

        return $columns;
    }

    public function getFilters(): array
    {
        $filters[] = $this->finder->getFilters();

        foreach ($this->plugins as $plugin) {
            $filters[] = $plugin->getFilters();
        }

        return array_merge(...$filters);
    }

    public function getAll(array $filters = [], ?string $orderBy = null, ?string $orderDir = null, ?int $limit = null, ?int $page = null): array
    {
        $qb = $this->getQueryBuilder();

        $this->applyOrderBy($qb, $orderBy, $orderDir);
        $this->applyPagination($qb, $limit, $page);
        $this->applyFilters($qb, $filters);

        $result = $this->finder->prepareResult(
            $this->finder->fetchAllAssociative($qb)
        );

        foreach ($this->plugins as $plugin) {
            $result = $plugin->prepareResult($result);
        }

        foreach ($result as $key => $row) {
            $actions = [];
            $actions[] = $this->finder->buildActions($row);

            foreach ($this->plugins as $plugin) {
                $actions[] = $plugin->buildActions($row);
            }

            foreach ($actions as $gk => $group) {
                foreach ($group as $ak => $action) {
                    if (is_string($action)) {
                        $actions[$gk][$ak] = [
                            'view' => $action,
                        ];
                    }
                }
            }

            $result[$key]['actions'] = array_merge(...$actions);

            foreach ($result[$key]['actions'] as $actionKey => $action) {
                $action = array_merge([
                    'view' => null,
                    'view_context' => [],
                ], $action);

                $data = array_merge($action['view_context'], [
                    'row' => $row,
                ]);

                $result[$key]['actions'][$actionKey] = $this->engine->render(new View($action['view'], $data));
            }
        }

        return $result;
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->getQueryBuilder();
        $qb->select('COUNT(*) AS count');
        $this->applyFilters($qb, $filters);

        $result = $qb->execute()->fetchAllAssociative();

        return (int) ($result[0]['count'] ?? 0);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->finder->getQueryBuilder();
        $queryBuilder = $this->finder->prepareQueryBuilder($queryBuilder);

        foreach ($this->plugins as $plugin) {
            $queryBuilder = $plugin->prepareQueryBuilder($queryBuilder);
        }

        foreach ($this->getColumns() as $name => $info) {
            if (isset($info['selector'])) {
                $queryBuilder->addSelect(sprintf('%s AS %s', $info['selector'], $name));
            }
        }

        return $queryBuilder;
    }

    private function applyOrderBy(QueryBuilder $qb, ?string $orderBy = null, ?string $orderDir = null): void
    {
        if (! $orderBy || ! $orderDir) {
            return;
        }

        $columns = $this->getColumns();

        if (isset($columns[$orderBy]['sortable']) === false || $columns[$orderBy]['sortable'] !== true) {
            return;
        }

        $qb->orderBy($orderBy, $orderDir);
    }

    private function applyPagination(QueryBuilder $qb, ?int $limit = null, ?int $page = null): void
    {
        if ($limit) {
            $qb->setMaxResults($limit);

            if ($page) {
                $page = $page <= 0 ? 1 : $page;
                $offset = ($page - 1) * $limit;

                $qb->setFirstResult($offset);
            }
        }
    }

    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        /** @var Filter $filter */
        foreach ($filters as $name => $filter) {
            $parameter = "filter_$name";

            switch ($filter->getComparison()) {
                case ComparisonOperatorsEnum::HAS:
                    $qb->andWhere(sprintf('%s LIKE :%s', $filter->getSelector(), $parameter));
                    $qb->setParameter($parameter, '%' . $filter->getValue() . '%');
                    break;
                case ComparisonOperatorsEnum::EQUAL:
                    $qb->andWhere(sprintf('%s = :%s', $filter->getSelector(), $parameter));
                    $qb->setParameter($parameter, $filter->getValue());
                    break;
                case ComparisonOperatorsEnum::LESS:
                    $qb->andWhere(sprintf('%s < :%s', $filter->getSelector(), $parameter));
                    $qb->setParameter($parameter, $filter->getValue());
                    break;
                case ComparisonOperatorsEnum::LESS_EQUAL:
                    $qb->andWhere(sprintf('%s <= :%s', $filter->getSelector(), $parameter));
                    $qb->setParameter($parameter, $filter->getValue());
                    break;
                case ComparisonOperatorsEnum::MORE:
                    $qb->andWhere(sprintf('%s > :%s', $filter->getSelector(), $parameter));
                    $qb->setParameter($parameter, $filter->getValue());
                    break;
                case ComparisonOperatorsEnum::MORE_EQUAL:
                    $qb->andWhere(sprintf('%s >= :%s', $filter->getSelector(), $parameter));
                    $qb->setParameter($parameter, $filter->getValue());
                    break;
                default:
                    // If comparison operator is not supported, we skip it.
                    continue 2;
            }
        }
    }
}
