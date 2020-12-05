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
class UsernameUniqueValidator extends ConstraintValidator
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UsernameUnique) {
            throw new UnexpectedTypeException($constraint, UsernameUnique::class);
        }

        if (! $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $root = $this->context->getRoot();

        $criteria = [
            'username' => $value,
            'id__not_in' => [$root->has('id') ? $root->get('id')->getData() : null]
        ];

        if ($constraint->id_not_in_fields) {
            $criteria['id__not_in'] = [];

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
            $this->context->buildViolation('usernameAlreadyUsedPleaseTypeAnother')
                ->atPath('username')
                ->addViolation();
        }
    }
}
