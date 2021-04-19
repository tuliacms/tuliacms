<?php

declare(strict_types=1);

namespace spec\Tulia\Component\Datatable;

use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Query\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Datatable\Finder\FinderInterface;
use Tulia\Component\Datatable\Plugin\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableSpec extends ObjectBehavior
{
    public function let(FinderInterface $finder, Request $request, TranslatorInterface $translator): void
    {
        $this->beConstructedWith($finder, $request, $translator);
    }

    public function it_should_return_columns_from_finder_and_plugins(
        FinderInterface $finder
    ): void {
        $finder->getColumns()->shouldBeCalledOnce()->willReturn(['finder' => []]);

        $this->addPlugin(new class extends AbstractPlugin {
            public function getColumns(): array { return ['plugin' => []]; }
            public function supports(string $configurationKey): bool { return true; }
        });

        $this->getColumns()->shouldEqual([
            'finder' => [],
            'plugin' => [],
        ]);
    }

    public function it_should_return_filters_from_finder_and_plugins(
        FinderInterface $finder
    ): void {
        $finder->getFilters()->shouldBeCalledOnce()->willReturn(['finder' => []]);

        $this->addPlugin(new class extends AbstractPlugin {
            public function getFilters(): array { return ['plugin' => []]; }
            public function supports(string $configurationKey): bool { return true; }
        });

        $this->getFilters()->shouldEqual([
            'finder' => [],
            'plugin' => [],
        ]);
    }

    public function it_should_call_both_finder_and_plugins_methods_when_get_all_to_produce_result_array(
        FinderInterface $finder,
        QueryBuilder $queryBuilder,
        ResultStatement $resultStatement,
        AbstractPlugin $plugin
    ): void {
        $rows = [['id' => 'id', 'name' => 'Name']];

        $queryBuilder->execute()->shouldBeCalled()->willReturn($resultStatement);
        $resultStatement->fetchAllAssociative()->shouldBeCalled()->willReturn($rows);

        $finder->getQueryBuilder()->shouldBeCalledOnce()->willReturn($queryBuilder);
        $finder->prepareQueryBuilder($queryBuilder)->shouldBeCalledOnce()->willReturn($queryBuilder);
        $finder->getColumns()->shouldBeCalled()->willReturn(['finder' => []]);
        $finder->prepareResult($rows)->shouldBeCalled()->willReturn($rows);
        $finder->buildActions($rows)->shouldBeCalled()->willReturn($rows);

        $plugin->prepareQueryBuilder($queryBuilder)->shouldBeCalledOnce()->willReturn($queryBuilder);
        $plugin->getColumns()->shouldBeCalled()->willReturn(['plugin' => []]);
        $plugin->prepareResult($rows)->shouldBeCalled()->willReturn($rows);
        $plugin->buildActions($rows)->shouldBeCalled()->willReturn($rows);

        $this->addPlugin($plugin);
        $this->getAll();
    }
}
