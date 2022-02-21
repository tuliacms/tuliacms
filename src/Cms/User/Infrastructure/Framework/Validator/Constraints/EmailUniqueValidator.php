<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Tulia\Cms\User\Query\Enum\ScopeEnum;
use Tulia\Cms\User\Query\FinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class EmailUniqueValidator extends ConstraintValidator
{
    protected FinderFactoryInterface $finderFactory;

    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
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

        $finder = $this->finderFactory->getInstance(ScopeEnum::INTERNAL);
        $finder->setCriteria($criteria);
        $finder->fetchRaw();
        $founded = $finder->getResult()->first();

        if ($founded) {
            $this->context->buildViolation('emailAlreadyUsedPleaseTypeAnother')
                ->atPath('email')
                ->addViolation();
        }
    }
}
