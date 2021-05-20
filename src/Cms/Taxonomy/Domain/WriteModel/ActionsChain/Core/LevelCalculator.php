<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TermActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Service\ChildrenTermsLevelRaiser;
use Tulia\Cms\Taxonomy\Domain\WriteModel\TermRepository;

/**
 * @author Adam Banaszkiewicz
 */
class LevelCalculator implements TermActionInterface
{
    private TermRepository $repository;

    private ChildrenTermsLevelRaiser $childrenTermsLevelRaiser;

    public function __construct(
        TermRepository $repository,
        ChildrenTermsLevelRaiser $childrenTermsLevelRaiser
    ) {
        $this->repository = $repository;
        $this->childrenTermsLevelRaiser = $childrenTermsLevelRaiser;
    }

    public static function supports(): array
    {
        return [
            'insert' => 100,
            'update' => 100,
        ];
    }

    public function execute(Term $term): void
    {
        if ($term->getParentId() !== null) {
            $parent = $this->repository->find($term->getParentId());

            $term->setLevel($parent->getLevel() + 1);
        }

        $this->childrenTermsLevelRaiser->riseLevelForChildren(
            $term->getId()->getId(),
            $term->getLevel()
        );
    }
}
