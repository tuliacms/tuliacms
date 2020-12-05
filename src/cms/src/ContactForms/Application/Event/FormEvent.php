<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tulia\Cms\ContactForms\Application\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
class FormEvent extends Event
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }
}
