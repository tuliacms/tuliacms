<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Node\Domain\NodeFlag\Exception\FlagNotFoundException;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;
use Tulia\Cms\Node\Domain\WriteModel\ActionsChain\NodeActionInterface;
use Tulia\Cms\Node\Domain\WriteModel\Exception\SingularFlagImposedOnMoreThanOneNodeException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeByFlagFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SingularFlagsValidator implements NodeActionInterface
{
    private NodeByFlagFinderInterface $byFlagFinder;

    private NodeFlagRegistryInterface $flagRegistry;

    public function __construct(NodeByFlagFinderInterface $byFlagFinder, NodeFlagRegistryInterface $flagRegistry)
    {
        $this->byFlagFinder = $byFlagFinder;
        $this->flagRegistry = $flagRegistry;
    }

    public static function supports(): array
    {
        return [
            'insert' => 100,
            'update' => 100,
        ];
    }

    /**
     * @throws SingularFlagImposedOnMoreThanOneNodeException
     * @throws FlagNotFoundException
     */
    public function execute(Node $node): void
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
