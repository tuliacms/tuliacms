<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Frontend\Form;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface ContactFormBuilderInterface
{
    public function build(Form $form, array $data = [], array $options = []): FormInterface;
}
