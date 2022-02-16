<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Shared\Pagination\Paginator;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term as QueryModelTerm;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    private NodeFinderInterface $nodeFinder;

    public function __construct(NodeFinderInterface $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    public function show(QueryModelTerm $term, Request $request): ViewInterface
    {
        $perPage = 9;
        $page = $request->query->getInt('page', 1);
        $this->getDocument()->setTitle($term->getTitle());

        $nodes = $this->nodeFinder->find([
            'category'  => $term->getId(),
            'page'      => $page,
            'per_page'  => $perPage,
        ], NodeFinderScopeEnum::LISTING);

        return $this->view([
            '@cms/taxonomy/term_id:' . $term->getId() . '.tpl',
            '@cms/taxonomy/term_type:' . $term->getType() . '.tpl',
            '@cms/taxonomy/term.tpl',
        ], [
            'term' => $term,
            'nodes' => $nodes,
            'paginator' => new Paginator($request, $nodes->totalRows(), $page, $perPage)
        ]);
    }
}
