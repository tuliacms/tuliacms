<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain\Core;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Enum\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\NodeActionInterface;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeCannotBeRemovedException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Ports\Domain\ReadModel\NodeFinderInterface;

/**
 * This class is responsible to detect if deleting node has children.
 * If has, exception is thrown to prevent delete and prevent mismatch
 * in Database.
 *
 * @author Adam Banaszkiewicz
 */
class NodeChildrenPreDeleteValidator implements NodeActionInterface
{
    private NodeFinderInterface $nodeFinder;

    private TranslatorInterface $translator;

    public function __construct(NodeFinderInterface $nodeFinder, TranslatorInterface $translator)
    {
        $this->nodeFinder = $nodeFinder;
        $this->translator = $translator;
    }

    public static function supports(): array
    {
        return [
            'delete' => 100,
        ];
    }

    /**
     * @throws NodeCannotBeRemovedException
     */
    public function execute(Node $node): void
    {
        $nodes = $this->nodeFinder->find([
            'children_of' => $node->getId(),
            'per_page' => 1,
        ], NodeFinderScopeEnum::INTERNAL);

        if ($nodes->count()) {
            throw new NodeCannotBeRemovedException($this->translator->trans('cannotDeleteDueToContainingChildren', ['name' => $node->getTitle()], 'validators'));
        }
    }
}
