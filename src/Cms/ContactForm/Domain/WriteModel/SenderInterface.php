<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\WriteModel;

use Tulia\Cms\ContactForm\Domain\ReadModel\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface SenderInterface
{
    public function send(Form $form, array $data): int;
}
