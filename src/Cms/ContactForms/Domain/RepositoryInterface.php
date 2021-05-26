<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain;

use Tulia\Cms\ContactForms\Domain\ValueObject\AggregateId;
use Tulia\Cms\ContactForms\Domain\Aggregate\Form;
use Tulia\Cms\ContactForms\Domain\Exception\FormNotFoundException;

/**
 * @author Adam Banaszkiewicz
 */
interface RepositoryInterface
{
    /**
     * @throws FormNotFoundException
     */
    public function find(AggregateId $id, string $locale): Form;

    public function insert(Form $form): void;

    public function update(Form $form): void;

    public function delete(Form $form): void;
}
