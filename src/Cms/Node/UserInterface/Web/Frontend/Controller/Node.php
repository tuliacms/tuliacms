<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Frontend\Controller;

use Tulia\Cms\Node\Domain\ReadModel\Model\Node AS Model;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    protected TermFinderInterface $termFinder;

    public function __construct(TermFinderInterface $termFinder)
    {
        $this->termFinder = $termFinder;
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
            return $this->termFinder->findOne(['id' => $node->getCategory()], TermFinderScopeEnum::SINGLE);
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
