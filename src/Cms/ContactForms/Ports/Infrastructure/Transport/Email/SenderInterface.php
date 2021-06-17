<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Ports\Infrastructure\Transport\Email;

use Tulia\Cms\ContactForms\Domain\ReadModel\Finder\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface SenderInterface
{
    public function send(Form $form, array $data): int;
}
