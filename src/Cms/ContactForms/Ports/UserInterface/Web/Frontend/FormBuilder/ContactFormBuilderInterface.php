<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Ports\UserInterface\Web\Frontend\FormBuilder;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContactForms\Domain\ReadModel\Finder\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface ContactFormBuilderInterface
{
    public function build(Form $form, array $data = [], array $options = []): FormInterface;
}
