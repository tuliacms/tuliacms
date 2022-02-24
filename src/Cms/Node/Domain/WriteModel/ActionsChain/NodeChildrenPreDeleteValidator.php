<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderScopeEnum;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeCannotBeRemovedException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;

/**
 * This class is responsible to detect if deleting node has children.
 * If has, exception is thrown to prevent delete and prevent mismatch
 * in Database.
 *
 * @author Adam Banaszkiewicz
 */
class NodeChildrenPreDeleteValidator implements AggregateActionInterface
{
    private NodeFinderInterface $nodeFinder;

    private TranslatorInterface $translator;

    public function __construct(NodeFinderInterface $nodeFinder, TranslatorInterface $translator)
    {
        $this->nodeFinder = $nodeFinder;
        $this->translator = $translator;
    }

    public static function listen(): array
    {
        return [
            'delete' => 100,
        ];
    }

    public static function supports(): string
    {
        return Node::class;
    }

    /**
     * @throws NodeCannotBeRemovedException
     */
    public function execute(AggregateRoot $node): void
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
