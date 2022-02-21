<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Tulia\Cms\User\Infrastructure\Framework\Validator\PasswordValidatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PasswordValidator extends ConstraintValidator
{
    protected PasswordValidatorInterface $passwordValidator;

    public function __construct(PasswordValidatorInterface $passwordValidator)
    {
        $this->passwordValidator = $passwordValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        if (! $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $validate = $this->passwordValidator->validate($value);

        switch ($validate) {
            case PasswordValidatorInterface::ERR_MIN_LENGTH:
                $this->context->buildViolation('passwordMustContainMinimumLength', [ 'length' => $this->passwordValidator->getMinLength() ])
                    ->atPath('password')
                    ->addViolation();
                break;
            case PasswordValidatorInterface::ERR_MIN_DIGITS:
                $this->context->buildViolation('passwordMustContainMinimumDigits', [ 'length' => $this->passwordValidator->getMinDigits() ])
                    ->atPath('password')
                    ->addViolation();
                break;
            case PasswordValidatorInterface::ERR_MIN_SPECIALS:
                $this->context->buildViolation('passwordMustContainMinimumSpecialChars', [ 'length' => $this->passwordValidator->getMinSpecialChars() ])
                    ->atPath('password')
                    ->addViolation();
                break;
            case PasswordValidatorInterface::ERR_MIN_BIG_LETTERS:
                $this->context->buildViolation('passwordMustContainMinimumBigLetters', [ 'length' => $this->passwordValidator->getMinBigLetters() ])
                    ->atPath('password')
                    ->addViolation();
                break;
            case PasswordValidatorInterface::ERR_MIN_SMALL_LETTERS:
                $this->context->buildViolation('passwordMustContainMinimumSmallLetters', [ 'length' => $this->passwordValidator->getMinSmallLetters() ])
                    ->atPath('password')
                    ->addViolation();
                break;
        }
    }
}
