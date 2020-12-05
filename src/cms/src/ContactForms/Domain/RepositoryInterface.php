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
     * @param AggregateId $id
     * @param string $locale
     *
     * @return Form
     *
     * @throws FormNotFoundException
     */
    public function find(AggregateId $id, string $locale): Form;

    /**
     * @param Form $form
     */
    public function save(Form $form): void;

    /**
     * @param Form $form
     */
    public function delete(Form $form): void;
}
