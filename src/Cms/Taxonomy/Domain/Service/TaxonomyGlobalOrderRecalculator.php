<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\Service;

use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyGlobalOrderRecalculator
{
    public function recalculate(Taxonomy $taxonomy): void
    {
        $levelToCheck = 1;

        do {
            $anyTermsOnCheckLevel = false;

            foreach ($taxonomy->terms() as $term) {
                if ($term->getLevel() !== $levelToCheck) {
                    continue;
                }

                $globalOrder = $this->generateGlobalOrder($taxonomy, $term);

                if ($globalOrder !== $term->getGlobalOrder()) {
                    $term->setGlobalOrder($globalOrder);
                }

                $anyTermsOnCheckLevel = true;
            }

            $levelToCheck++;
        } while ($anyTermsOnCheckLevel && $levelToCheck <= 100);
    }

    private function getMaxLength(): int
    {
        return \strlen((string) PHP_INT_MAX);
    }

    private function generateGlobalOrder(Taxonomy $taxonomy, Term $term): int
    {
        if ($term->getParentId() === null) {
            $globalOrder = $term->getPosition();
        } else {
            $globalOrder = substr_replace(
                (string) $taxonomy->getTerm($term->getParentId())->getGlobalOrder(),
                (string) $term->getPosition(),
                $term->getLevel() - 1,
                1
            );
        }

        return (int) str_pad((string) $globalOrder, $this->getMaxLength(), '0');
    }
}
