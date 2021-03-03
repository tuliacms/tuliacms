<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Sender;

use Tulia\Cms\ContactForms\Query\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface SenderInterface
{
    public function send(Form $form, array $data): int;
}