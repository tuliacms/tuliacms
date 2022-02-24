<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class EmailUniqueValidator extends ConstraintValidator
{
    protected UserFinderInterface $userFinder;

    public function __construct(UserFinderInterface $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailUnique) {
            throw new UnexpectedTypeException($constraint, EmailUnique::class);
        }

        if (! $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $criteria = [
            'email' => $value,
            'id__not_in' => [],
        ];

        $root = $this->context->getRoot();

        if ($root->has('id') && $root->get('id')->getData()) {
            $criteria['id__not_in'] = [$root->get('id')->getData()];
        }

        if ($constraint->id_not_in_fields !== []) {
            foreach ($constraint->id_not_in_fields as $field) {
                $value = $root->get($field)->getData();

                if ($value) {
                    $criteria['id__not_in'][] = $value;
                }
            }
        }

        $founded = $this->userFinder->findOne($criteria, UserFinderScopeEnum::INTERNAL);

        if ($founded) {
            $this->context->buildViolation('emailAlreadyUsedPleaseTypeAnother')
                ->atPath('email')
                ->addViolation();
        }
    }
}
