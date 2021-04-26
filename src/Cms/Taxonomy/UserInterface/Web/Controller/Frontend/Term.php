<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Controller\Frontend;

use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\Exception\QueryException;
use Tulia\Cms\Node\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Query\FinderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Query\Model\Term as QueryModelTerm;
use Tulia\Component\Templating\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    protected FinderFactoryInterface $nodeFinderFactory;

    public function __construct(FinderFactoryInterface $nodeFinderFactory)
    {
        $this->nodeFinderFactory = $nodeFinderFactory;
    }

    /**
     * @param QueryModelTerm $term
     * @param Request $request
     *
     * @return ViewInterface
     *
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    public function show(QueryModelTerm $term, Request $request): ViewInterface
    {
        $this->getDocument()->setTitle($term->getName());

        $finder = $this->createNodeFinder($term, $request->query->getInt('page', 1));

        return $this->view([
            '@cms/taxonomy/term_id:' . $term->getId() . '.tpl',
            '@cms/taxonomy/term_type:' . $term->getType() . '.tpl',
            '@cms/taxonomy/term.tpl',
        ], [
            'term'      => $term,
            'nodes'     => $finder->getResult(),
            'paginator' => $finder->getPaginator($request),
        ]);
    }

    private function createNodeFinder(QueryModelTerm $term, int $page): FinderInterface
    {
        $finder = $this->nodeFinderFactory->getInstance(ScopeEnum::LISTING);
        $finder->setCriteria([
            'node_type' => null,
            'category'  => $term->getId(),
            'page'      => $page,
            'per_page'  => 9,
        ]);
        $finder->fetch();

        return $finder;
    }
}
