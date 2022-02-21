<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Helper;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class TermsChangelog
{
    private array $termsChanges = [
        'insert' => [],
        'update' => [],
        'delete' => [],
    ];

    public function collectChangedTerms(): array
    {
        return $this->termsChanges;
    }

    public function clearTermsChangelog(): void
    {
        $this->termsChanges = [
            'insert' => [],
            'update' => [],
            'delete' => [],
        ];
    }

    public function insert(Term $term): void
    {
        if ($this->alreadyInserted($term)) {
            return;
        }

        $this->termsChanges['insert'][$term->getId()->getValue()] = $term;

        unset(
            $this->termsChanges['update'][$term->getId()->getValue()],
            $this->termsChanges['delete'][$term->getId()->getValue()]
        );
    }

    public function update(Term $term): void
    {
        if ($this->alreadyInserted($term) || $this->alreadyUpdated($term) || $this->alreadyRemoved($term)) {
            return;
        }

        $this->termsChanges['update'][$term->getId()->getValue()] = $term;
    }

    public function delete(Term $term): void
    {
        if ($this->alreadyRemoved($term)) {
            return;
        }

        $this->termsChanges['delete'][$term->getId()->getValue()] = $term;

        unset(
            $this->termsChanges['insert'][$term->getId()->getValue()],
            $this->termsChanges['update'][$term->getId()->getValue()]
        );
    }

    private function alreadyInserted(Term $term): bool
    {
        return isset($this->termsChanges['insert'][$term->getId()->getValue()]);
    }

    private function alreadyUpdated(Term $term): bool
    {
        return isset($this->termsChanges['update'][$term->getId()->getValue()]);
    }

    private function alreadyRemoved(Term $term): bool
    {
        return isset($this->termsChanges['delete'][$term->getId()->getValue()]);
    }
}
