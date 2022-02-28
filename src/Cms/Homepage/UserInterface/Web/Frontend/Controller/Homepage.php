<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Frontend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Node\Domain\NodeFlag\Enum\NodeFlagEnum;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Homepage extends AbstractController
{
    private NodeFinderInterface $nodeFinder;
    private Node $nodeController;

    public function __construct(NodeFinderInterface $nodeFinder, Node $nodeController)
    {
        $this->nodeFinder = $nodeFinder;
        $this->nodeController = $nodeController;
    }

    /**
     * @return Response|ViewInterface
     */
    public function index(Request $request)
    {
        $homepage = $this->nodeFinder->findOne([
            'flag' => NodeFlagEnum::PAGE_HOMEPAGE,
        ], NodeFinderScopeEnum::SINGLE);

        if ($homepage) {
            return $this->nodeController->show($homepage, $request);
        }

        return $this->view('@cms/homepage/index.tpl');
    }
}
