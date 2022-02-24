<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain;

use Tulia\Cms\Node\Domain\NodeFlag\Exception\FlagNotFoundException;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Exception\SingularFlagImposedOnMoreThanOneNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByFlagFinderInterface;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\AggregateRoot;

/**
 * @author Adam Banaszkiewicz
 */
class SingularFlagsValidator implements AggregateActionInterface
{
    private NodeByFlagFinderInterface $byFlagFinder;
    private NodeFlagRegistryInterface $flagRegistry;

    public function __construct(NodeByFlagFinderInterface $byFlagFinder, NodeFlagRegistryInterface $flagRegistry)
    {
        $this->byFlagFinder = $byFlagFinder;
        $this->flagRegistry = $flagRegistry;
    }

    public static function listen(): array
    {
        return [
            'insert' => 100,
            'update' => 100,
        ];
    }

    public static function supports(): string
    {
        return Node::class;
    }

    /**
     * @throws SingularFlagImposedOnMoreThanOneNodeException
     * @throws FlagNotFoundException
     */
    public function execute(AggregateRoot $node): void
    {
        $singularFlags = [];

        foreach ($node->getFlags() as $flag) {
            if ($this->flagRegistry->isSingular($flag)) {
                $singularFlags[] = $flag;
            }
        }

        $otherNodes = $this->byFlagFinder->findOtherNodesWithFlags($node->getId()->getValue(), $singularFlags, $node->getWebsiteId());

        if ($otherNodes !== []) {
            throw SingularFlagImposedOnMoreThanOneNodeException::fromFlag($otherNodes[0]['flag']);
        }
    }
}
