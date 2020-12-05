<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator;

use Tulia\Cms\Options\OptionsInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UsernameValidatorFactory
{
    /**
     * @param OptionsInterface $options
     *
     * @return UsernameValidatorInterface
     */
    public static function factory(OptionsInterface $options): UsernameValidatorInterface
    {
        $validator = new UsernameValidator();
        $validator->setMinLength((int) $options->get('users.username.min_length', 4));

        return $validator;
    }
}
