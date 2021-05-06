<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator;

use Tulia\Cms\Options\Domain\ReadModel\Options;

/**
 * @author Adam Banaszkiewicz
 */
class PasswordValidatorFactory
{
    public static function factory(Options $options): PasswordValidatorInterface
    {
        $validator = new PasswordValidator();
        $validator->setMinLength((int) $options->get('users.password.min_length', 4));
        $validator->setMinDigits((int) $options->get('users.password.min_digits', 1));
        $validator->setMinSpecialChars((int) $options->get('users.password.min_special_chars', 1));
        $validator->setMinBigLetters((int) $options->get('users.password.min_big_letters', 1));
        $validator->setMinSmallLetters((int) $options->get('users.password.min_small_letters', 1));

        return $validator;
    }
}
