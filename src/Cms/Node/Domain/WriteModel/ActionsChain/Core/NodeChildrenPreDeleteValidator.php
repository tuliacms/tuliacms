<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Tulia\Cms\Node\Application\Exception\TranslatableNodeException;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Enum\ScopeEnum;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\ActionInterface;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\NodeFinderInterface;

/**
 * This class is responsible to detect if deleting node has children.
 * If has, exception is thrown to prevent delete and prevent mismatch
 * in Database.
 *
 * @author Adam Banaszkiewicz
 */
class NodeChildrenPreDeleteValidator implements ActionInterface
{
    protected NodeFinderInterface $nodeFinder;

    public function __construct(NodeFinderInterface $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    public static function supports(): array
    {
        return [
            'delete' => 100,
        ];
    }

    public function execute(Node $node): void
    {
        $nodes = $this->nodeFinder->find([
            'children_of' => $node->getId(),
            'per_page' => 1,
        ], ScopeEnum::INTERNAL);

        if ($nodes->count()) {
            $e = new TranslatableNodeException('cannotDeleteDueToContainingChildren');
            $e->setParameters(['name' => $node->getTitle()]);
            $e->setDomain('validators');

            throw $e;
        }
    }
}
