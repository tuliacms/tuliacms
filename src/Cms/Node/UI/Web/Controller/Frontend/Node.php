<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UI\Web\Controller\Frontend;

use Tulia\Cms\Node\Query\Model\Node as QueryModelNode;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    /**
     * @var FinderFactoryInterface
     */
    protected $termFinderFactory;

    /**
     * @param FinderFactoryInterface $termFinderFactory
     */
    public function __construct(FinderFactoryInterface $termFinderFactory)
    {
        $this->termFinderFactory = $termFinderFactory;
    }

    /**
     * @param QueryModelNode $node
     *
     * @return ViewInterface
     */
    public function show(QueryModelNode $node): ViewInterface
    {
        $this->getDocument()->setTitle($node->getTitle());

        $category = $this->findCategory($node);

        return $this->view($this->createViews($node, $category), [
            'node'     => $node,
            'category' => $category,
        ]);
    }

    private function findCategory(QueryModelNode $node): ?Term
    {
        if ($node->getCategory()) {
            return $this->termFinderFactory
                ->getInstance(ScopeEnum::SINGLE)
                ->find($node->getCategory());
        }

        return null;
    }

    private function createViews(QueryModelNode $node, ?Term $category): array
    {
        $views = [];
        $views[] = '@cms/node/node_id:' . $node->getId() . '.tpl';
        $views[] = '@cms/node/node_type:' . $node->getType() . '.tpl';

        if ($category) {
            $views[] = '@cms/node/node_type:' . $node->getType() . '_taxonomy:' . $category->getType() . '.tpl';
            $views[] = '@cms/node/node_taxonomy:' . $category->getType() . '.tpl';
        }

        $views[] = '@cms/node/node.tpl';

        return $views;
    }
}
