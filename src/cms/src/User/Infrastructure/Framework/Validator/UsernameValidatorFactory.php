<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator;

use Tulia\Cms\Options\Application\Service\Options;

/**
 * @author Adam Banaszkiewicz
 */
class UsernameValidatorFactory
{
    public static function factory(Options $options): UsernameValidatorInterface
    {
        $validator = new UsernameValidator();
        $validator->setMinLength((int) $options->get('users.username.min_length', 4));

        return $validator;
    }
}
