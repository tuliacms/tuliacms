<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Frontend\Controller;

use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node AS Model;
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
    protected FinderFactoryInterface $termFinderFactory;

    public function __construct(FinderFactoryInterface $termFinderFactory)
    {
        $this->termFinderFactory = $termFinderFactory;
    }

    public function show(Model $node): ViewInterface
    {
        $this->getDocument()->setTitle($node->getTitle());

        $category = $this->findCategory($node);

        return $this->view($this->createViews($node, $category), [
            'node'     => $node,
            'category' => $category,
        ]);
    }

    private function findCategory(Model $node): ?Term
    {
        if ($node->getCategory()) {
            return $this->termFinderFactory
                ->getInstance(ScopeEnum::SINGLE)
                ->find($node->getCategory());
        }

        return null;
    }

    private function createViews(Model $node, ?Term $category): array
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
