<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Query\Factory;

use Tulia\Cms\ContactForms\Query\Model\Form;

/**
 * @author Adam Banaszkiewicz
 */
interface FormFactoryInterface
{
    /**
     * Creates new Form object, with loaded metadata object (ready to sync),
     * and with default values given in $data array. Sets also object ID and is ready to
     * store in database.
     *
     * @param array $data
     *
     * @return Form
     */
    public function createNew(array $data = []): Form;
}
