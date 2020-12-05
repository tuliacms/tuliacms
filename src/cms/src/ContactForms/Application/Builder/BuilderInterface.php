<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Builder;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContactForms\Query\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface BuilderInterface
{
    public function build(Form $form, array $data = [], array $options = []): FormInterface;
}
