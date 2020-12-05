<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Tulia\Cms\User\Infrastructure\Framework\Validator\UsernameValidatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UsernameValidator extends ConstraintValidator
{
    /**
     * @var UsernameValidatorInterface
     */
    protected $usernameValidator;

    /**
     * @param UsernameValidatorInterface $usernameValidator
     */
    public function __construct(UsernameValidatorInterface $usernameValidator)
    {
        $this->usernameValidator = $usernameValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Username) {
            throw new UnexpectedTypeException($constraint, Username::class);
        }

        if (! $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $validate = $this->usernameValidator->validate($value);

        switch ($validate) {
            case UsernameValidatorInterface::ERR_MIN_LENGTH:
                $this->context->buildViolation('usernameMustContainMinimumLength', [ 'length' => $this->usernameValidator->getMinLength() ])
                    ->atPath('username')
                    ->addViolation();
                break;
            case UsernameValidatorInterface::ERR_NOT_ALLOWED_CHARS:
                $this->context->buildViolation('usernameContainsNotAllowedCharactersAllowedAre', [ 'allowed_characters' => '"a-z 0-9 - _ @ ."' ])
                    ->atPath('username')
                    ->addViolation();
                break;
        }
    }
}
