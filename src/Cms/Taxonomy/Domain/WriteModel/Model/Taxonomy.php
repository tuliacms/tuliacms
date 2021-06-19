<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Tulia\Cms\Platform\Domain\WriteModel\Model\AggregateRoot;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermCreated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermDeleted;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Helper\TermsChangelog;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;

/**
 * @author Adam Banaszkiewicz
 */
class Taxonomy extends AggregateRoot
{
    private TaxonomyTypeInterface $type;

    private string $websiteId;

    /**
     * @var Term[]
     */
    private array $terms = [];

    private TermsChangelog $changelog;

    private function __construct(TaxonomyTypeInterface $type, string $websiteId, array $terms = [])
    {
        $this->type = $type;
        $this->websiteId = $websiteId;

        foreach ($terms as $term) {
            $term['taxonomy'] = $this;
            $this->terms[$term['id']] = Term::buildFromArray($term);
            $this->terms[$term['id']]->setTaxonomy($this, $this->produceTermChangeCallback());
        }

        $this->changelog = new TermsChangelog();
    }

    public static function create(TaxonomyTypeInterface $type, string $websiteId, array $terms = []): self
    {
        return new self($type, $websiteId, $terms);
    }

    public function getType(): TaxonomyTypeInterface
    {
        return $this->type;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @return Term[]
     */
    public function terms(): iterable
    {
        foreach ($this->terms as $term) {
            yield $term;
        }
    }

    /**
     * @throws TermNotFoundException
     */
    public function getTerm(TermId $id): Term
    {
        if (isset($this->terms[$id->getId()])) {
            return $this->terms[$id->getId()];
        }

        throw new TermNotFoundException(sprintf('Term %s not found.', $id->getId()));
    }

    public function addTerm(Term $term): void
    {
        $this->terms[$term->getId()->getId()] = $term;
        $term->setTaxonomy($this, $this->produceTermChangeCallback());

        if ($term->isRoot() === false) {
            $this->resolveItemParent($term);
            $this->calculateItemLevel($term);
            $this->calculateItemPosition($term);
        }

        $this->changelog->insert($term);

        $this->recordThat(TermCreated::fromTerm($term));
    }

    public function removeTerm(Term $term): void
    {
        if (isset($this->terms[$term->getId()->getId()]) === false) {
            return;
        }

        $this->removeTermChildren($term);

        unset($this->terms[$term->getId()->getId()]);
        $term->setTaxonomy($this, null);

        $this->changelog->delete($term);

        $this->recordThat(TermDeleted::fromTerm($term));
    }

    public function collectChangedTerms(): array
    {
        return $this->changelog->collectChangedTerms();
    }

    public function clearTermsChangelog(): void
    {
        $this->changelog->clearTermsChangelog();
    }

    private function produceTermChangeCallback(): callable
    {
        return function (Term $term) {
            $this->changelog->update($term);
        };
    }

    private function calculateItemLevel(Term $term): void
    {
        $parent = $this->getTerm($term->getParentId());
        $term->setLevel($parent->getLevel() + 1);
    }

    private function calculateItemPosition(Term $term): void
    {
        if ($term->getPosition() === 0) {
            $position = 0;

            foreach ($this->terms as $existingItem) {
                if ($existingItem->getParentId() === null) {
                    continue;
                }

                if ($existingItem->getParentId()->equals($term->getParentId())) {
                    $position = max($position, $existingItem->getPosition());
                }
            }

            $term->setPosition($position + 1);
        }
    }

    private function resolveItemParent(Term $term): void
    {
        if ($term->getParentId() === null) {
            $term->setParentId(new TermId(Term::ROOT_ID));
        }
    }

    private function removeTermChildren(Term $term): void
    {
        foreach ($this->terms as $existingTerm) {
            if ($existingTerm->getParentId() === null) {
                continue;
            }

            if ($existingTerm->getParentId()->equals($term->getId())) {
                $this->removeTerm($existingTerm);
            }
        }
    }
}
