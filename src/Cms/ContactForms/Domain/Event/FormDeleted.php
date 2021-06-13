<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

use Tulia\Cms\ContactForms\Domain\WriteModel\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
class FormDeleted extends DomainEvent
{
    public static function fromForm(Form $form): self
    {
        return new self($form->getId()->getId());
    }
}
