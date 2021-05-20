<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model;

use Tulia\Cms\Platform\Domain\Aggregate\AggregateRoot;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermCreated;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Event\TermDeleted;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Helper\TermsChangelog;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TaxonomyType;

/**
 * @author Adam Banaszkiewicz
 */
class Taxonomy extends AggregateRoot
{
    private TaxonomyTypeInterface $type;

    /**
     * @var Term[]
     */
    private array $terms = [];

    private TermsChangelog $changelog;

    private function __construct(TaxonomyTypeInterface $type, array $terms = [])
    {
        $this->type = $type;

        foreach ($terms as $term) {
            $this->terms[$term->getId()->getId()] = $term;
            $term->setTaxonomy($this, $this->produceTermChangeCallback());
        }

        $this->changelog = new TermsChangelog();
    }

    public static function createNew(TaxonomyTypeInterface $type): self
    {
        return new self($type);
    }

    public static function buildFromArray(array $data): self
    {
        return new self($data['type'], $data['terms']);
    }

    public function getType(): TaxonomyTypeInterface
    {
        return $this->type;
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
    public function getTerm(string $id): Term
    {
        if (isset($this->terms[$id])) {
            return $this->terms[$id];
        }

        throw new TermNotFoundException(sprintf('Term %s not found.', $id));
    }

    public function addTerm(Term $term): void
    {
        $this->terms[$term->getId()->getId()] = $term;
        $term->setTaxonomy($this, $this->produceTermChangeCallback());

        $this->changelog->insert($term);

        $this->recordThat(TermCreated::fromTerm($term));
    }

    public function removeTerm(Term $term): void
    {
        if (isset($this->terms[$term->getId()->getId()]) === false) {
            return;
        }

        unset($this->terms[$term->getId()->getId()]);
        $term->setTaxonomy(null, null);

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
}
