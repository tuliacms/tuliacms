<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author Adam Banaszkiewicz
 */
class UsernameUnique extends Constraint
{
    /**
     * @var array
     */
    public $id_not_in_fields = [];
}
