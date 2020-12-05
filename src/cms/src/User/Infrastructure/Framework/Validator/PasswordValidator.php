<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator;

/**
 * @author Adam Banaszkiewicz
 */
class PasswordValidator implements PasswordValidatorInterface
{
    protected $minLength       = 8;
    protected $minDigits       = 1;
    protected $minSpecialChars = 1;
    protected $minBigLetters   = 1;
    protected $minSmallLetters = 1;

    /**
     * {@inheritdoc}
     */
    public function validate(string $password): int
    {
        if (mb_strlen($password) < $this->minLength) {
            return PasswordValidatorInterface::ERR_MIN_LENGTH;
        }

        $digitsLeft = preg_replace('/[^0-9]+/', '', $password);

        if (mb_strlen($digitsLeft) < $this->minDigits) {
            return PasswordValidatorInterface::ERR_MIN_DIGITS;
        }

        $specialsLeft = preg_replace('/[0-9a-zA-Z]+/', '', $password);

        if (mb_strlen($specialsLeft) < $this->minSpecialChars) {
            return PasswordValidatorInterface::ERR_MIN_SPECIALS;
        }

        $bigLettersLeft = preg_replace('/[^A-Z]+/', '', $password);

        if (mb_strlen($bigLettersLeft) < $this->minBigLetters) {
            return PasswordValidatorInterface::ERR_MIN_BIG_LETTERS;
        }

        $smallLettersLeft = preg_replace('/[^a-z]+/', '', $password);

        if (mb_strlen($smallLettersLeft) < $this->minSmallLetters) {
            return PasswordValidatorInterface::ERR_MIN_SMALL_LETTERS;
        }

        return PasswordValidatorInterface::OK;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinLength(int $minLength): void
    {
        $this->minLength = $minLength;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinDigits(): int
    {
        return $this->minDigits;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinDigits(int $minDigits): void
    {
        $this->minDigits = $minDigits;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSpecialChars(): int
    {
        return $this->minSpecialChars;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinSpecialChars(int $minSpecialChars): void
    {
        $this->minSpecialChars = $minSpecialChars;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinBigLetters(): int
    {
        return $this->minBigLetters;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinBigLetters(int $minBigLetters): void
    {
        $this->minBigLetters = $minBigLetters;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSmallLetters(): int
    {
        return $this->minSmallLetters;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinSmallLetters(int $minSmallLetters): void
    {
        $this->minSmallLetters = $minSmallLetters;
    }
}
