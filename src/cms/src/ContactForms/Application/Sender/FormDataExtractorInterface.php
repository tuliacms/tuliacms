<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Sender;

use Tulia\Cms\ContactForms\Query\Model\Form;
use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface FormDataExtractorInterface
{
    public function extract(Form $model, FormInterface $form): array;
}
