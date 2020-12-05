<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Policy;

use Tulia\Cms\ContactForms\Domain\Exception\DomainException;

/**
 * @author Adam Banaszkiewicz
 */
interface FieldsTemplatePolicyInterface
{
    /**
     * @param string|null $template
     *
     * @return bool
     *
     * @throws DomainException
     *
     * Policy can return boolean false, or can throw any domain event
     * to prevent Aggregate action.
     */
    public function templateCanBeApplied(?string $template): bool;
}
